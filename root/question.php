<?php
require_once('../includes/auth.php');

if (!isset($_SESSION['user'])) {
	header('Location: /');
	return;
}

function genType () {
	return 'integer';
}

function genScaledValue ($scale) {
	return mt_rand(0, $scale['none']) == 0 ? intval(floor(
		log(
			mt_rand() * (pow($scale['exp'], $scale['max'] + 1) - pow($scale['exp'], $scale['min'])) / mt_getrandmax() + pow($scale['exp'], $scale['min']),
			$scale['exp']
		)
	)) : 0;
}

function genConfirmation() {
	return 'none';
}

$choiceLenScale = array('min' => 2, 'max' => 8, 'exp' => 0.9, 'none' => 0);
$itemLenScale = array('min' => 1, 'max' => 1, 'exp' => 0.5, 'none' => 0);
$itemBitsScale = array('min' => 3, 'max' => 10, 'exp' => 0.6, 'none' => 0);
$minTimeScale = array('min' => 2, 'max' => 8, 'exp' => 0.75, 'none' => 5);
$maxTimeScale = array('min' => 2, 'max' => 8, 'exp' => 0.8, 'none' => 5);

$mysqlCredentials = json_decode(file_get_contents('../mysql-credentials.json'), true);

$conn = mysqli_connect($mysqlCredentials["host"], $mysqlCredentials["user"], $mysqlCredentials["password"], $mysqlCredentials["database"]);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['set'])) {
	$_SESSION['set'] = array(
		'type' => genType(),
		'ind' => 0,
		'len' => mt_rand(1, 12),
		'choiceLen' => mt_rand(0, 3) > 0 ? genScaledValue($choiceLenScale) : NULL,
		'itemLen' => mt_rand(0, 3) > 0 ? genScaledValue($itemLenScale) : NULL,
		'itemBits' => mt_rand(0, 3) > 0 ? genScaledValue($itemBitsScale) : NULL,
		'minTime' => mt_rand(0, 3) > 0 ? genScaledValue($minTimeScale) : NULL,
		'maxTime' => mt_rand(0, 3) > 0 ? genScaledValue($maxTimeScale) : NULL,
		'confirmation' => mt_rand(0, 3) > 0 ? genConfirmation() : NULL
	);

	$sqlCreateSet = $conn->prepare("INSERT INTO sets (session, user, type, setLen, choiceLen, itemLen, itemBits, minTime, maxTime, confirmation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$sqlCreateSetSessionID = session_id();
	$sqlCreateSetUser = $_SESSION['user'] ? $_SESSION['user'] : NULL;
	$sqlCreateSet->bind_param(
		"sssiiiiiis",
		$sqlCreateSetSessionID,
		$sqlCreateSetUser,
		$_SESSION['set']['type'],
		$_SESSION['set']['len'],
		$_SESSION['set']['choiceLen'],
		$_SESSION['set']['itemLen'],
		$_SESSION['set']['itemBits'],
		$_SESSION['set']['minTime'],
		$_SESSION['set']['maxTime'],
		$_SESSION['set']['confirmation']
	);
	$sqlCreateSet->execute();
	$_SESSION['set']['id'] = $sqlCreateSet->insert_id;
	$sqlCreateSet->close();
}

// $remoteCmd = 'python predictback/test.py '.strval($_SESSION['set']['id']).' '.strval($_SESSION['set']['ind']);
// $localCmd = 'ssh lenovodesktop "'.$remoteCmd.'"';
// $literalCmd = "bash -c 'exec nohup setsid ".$localCmd." > /dev/null 2>&1 &'";
// exec($literalCmd);

$loadAnimation = !isset($_SESSION['question']);

$_SESSION['question'] = array(
	'setInd' => $_SESSION['set']['ind'],
	'choiceLen' => isset($_SESSION['set']['choiceLen']) ? $_SESSION['set']['choiceLen'] : genScaledValue($choiceLenScale),
	'itemLen' => isset($_SESSION['set']['itemLen']) ? $_SESSION['set']['itemLen'] : genScaledValue($itemLenScale),
	'itemBits' => isset($_SESSION['set']['itemBits']) ? $_SESSION['set']['itemBits'] : genScaledValue($itemBitsScale),
	'minTime' => isset($_SESSION['set']['minTime']) ? $_SESSION['set']['minTime'] : genScaledValue($minTimeScale),
	'maxTime' => isset($_SESSION['set']['maxTime']) ? $_SESSION['set']['maxTime'] : genScaledValue($maxTimeScale),
	'confirmation' => isset($_SESSION['set']['confirmation']) ? $_SESSION['set']['confirmation'] : genConfirmation(),
	'list' => array(),
	'display' => array(),
	'displayWidth' => 0
);

$sqlCreateQuestion = $conn->prepare("INSERT INTO questions (setID, setInd, choiceLen, itemLen, itemBits, minTime, maxTime, confirmation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$sqlCreateQuestion->bind_param(
	"iiiiiiis",
	$_SESSION['set']['id'],
	$_SESSION['question']['setInd'],
	$_SESSION['question']['choiceLen'],
	$_SESSION['question']['itemLen'],
	$_SESSION['question']['itemBits'],
	$_SESSION['question']['minTime'],
	$_SESSION['question']['maxTime'],
	$_SESSION['question']['confirmation']
);
$sqlCreateQuestion->execute();
$_SESSION['question']['id'] = $sqlCreateQuestion->insert_id;
$sqlCreateQuestion->close();

$uniqueChoices = 0;

while ($uniqueChoices < $_SESSION['question']['choiceLen']) {
	$sqlCreateChoice = $conn->prepare("INSERT INTO choices (questionID) VALUES (?)");
	$sqlCreateChoice->bind_param(
		"i",
		$_SESSION['question']['id']
	);
	$sqlCreateChoice->execute();
	$nextChoiceId = $sqlCreateChoice->insert_id;
	$sqlCreateChoice->close();

	$choice = array();

	for ($i = 0; $i < $_SESSION['question']['itemLen']; $i++) {
		$num = mt_rand(0, pow(2, $_SESSION['question']['itemBits']) - 1);
		array_push($choice, $num);

		$sqlCreateItem = $conn->prepare("INSERT INTO items (choiceID, num) VALUES (?, ?)");
		$sqlCreateItem->bind_param(
			"ii",
			$nextChoiceId,
			$num
		);
		$sqlCreateItem->execute();
		$sqlCreateItem->close();
	}

	if (!in_array($choice, $_SESSION['question']['list'])) {
		array_push($_SESSION['question']['list'], $choice);
		
		$sqlConfirmChoice = $conn->prepare("UPDATE choices SET valid = TRUE WHERE id = ?");
		$sqlConfirmChoice->bind_param(
			"i",
			$nextChoiceId
		);
		$sqlConfirmChoice->execute();
		$sqlConfirmChoice->close();

		$uniqueChoices++;
	}
}

$conn->close();

foreach ($_SESSION['question']['list'] as $choice) {
	if ($_SESSION['set']['type'] == 'integer') {
		$displayVal = $choice[0];
		$displayLen = strlen($displayVal) + 2;
	}

	array_push($_SESSION['question']['display'], $displayVal);
	$_SESSION['question']['displayWidth'] = max($_SESSION['question']['displayWidth'], $displayLen);
}

$setInd = $_SESSION['set']['ind'];
$setLen = $_SESSION['set']['len'];
$minTime = $_SESSION['question']['minTime'];
$maxTime = $_SESSION['question']['maxTime'];

$pageName = 'question';
$pageDisplay = 'Question';

require_once('../includes/page.php');