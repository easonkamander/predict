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

if (isset($_SESSION['set'])) {
	$sqlGetSet = $conn->prepare("SELECT id, type, setInd, setLen, choiceLen, itemLen, itemBits, minTime, maxTime, confirmation FROM sets WHERE id = ?");
	$sqlGetSet->bind_param(
		"i",
		$_SESSION['set']
	);
	$sqlGetSet->execute();
	$set = $sqlGetSet->get_result()->fetch_assoc();
	$sqlGetSet->close();
} else {
	$set = array(
		'type' => genType(),
		'setInd' => 0,
		'setLen' => 12,
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
		$set['type'],
		$set['setLen'],
		$set['choiceLen'],
		$set['itemLen'],
		$set['itemBits'],
		$set['minTime'],
		$set['maxTime'],
		$set['confirmation']
	);
	$sqlCreateSet->execute();
	$set['id'] = $sqlCreateSet->insert_id;
	$sqlCreateSet->close();

	$_SESSION['set'] = $set['id'];
}

$loadAnimation = !isset($_SESSION['question']);

$question = array(
	'setInd' => $set['setInd'],
	'choiceLen' => isset($set['choiceLen']) ? $set['choiceLen'] : genScaledValue($choiceLenScale),
	'itemLen' => isset($set['itemLen']) ? $set['itemLen'] : genScaledValue($itemLenScale),
	'itemBits' => isset($set['itemBits']) ? $set['itemBits'] : genScaledValue($itemBitsScale),
	'minTime' => isset($set['minTime']) ? $set['minTime'] : genScaledValue($minTimeScale),
	'maxTime' => isset($set['maxTime']) ? $set['maxTime'] : genScaledValue($maxTimeScale),
	'confirmation' => isset($set['confirmation']) ? $set['confirmation'] : genConfirmation(),
);

$sqlCreateQuestion = $conn->prepare("INSERT INTO questions (setID, setInd, choiceLen, itemLen, itemBits, minTime, maxTime, confirmation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$sqlCreateQuestion->bind_param(
	"iiiiiiis",
	$set['id'],
	$question['setInd'],
	$question['choiceLen'],
	$question['itemLen'],
	$question['itemBits'],
	$question['minTime'],
	$question['maxTime'],
	$question['confirmation']
);
$sqlCreateQuestion->execute();
$question['id'] = $sqlCreateQuestion->insert_id;
$sqlCreateQuestion->close();

$_SESSION['question'] = $question['id'];

$choices = array();
$chash = array();
$displayWidth = 0;

$uniqueChoices = 0;

while ($uniqueChoices < $question['choiceLen']) {
	$sqlCreateChoice = $conn->prepare("INSERT INTO choices (questionID, choiceInd) VALUES (?, ?)");
	$sqlCreateChoice->bind_param(
		"ii",
		$question['id'],
		$uniqueChoices
	);
	$sqlCreateChoice->execute();
	$nextChoiceId = $sqlCreateChoice->insert_id;
	$sqlCreateChoice->close();

	$choice = array(
		'items' => array()
	);

	for ($i = 0; $i < $question['itemLen']; $i++) {
		$num = mt_rand(0, pow(2, $question['itemBits']) - 1);
		array_push($choice['items'], $num);

		$sqlCreateItem = $conn->prepare("INSERT INTO items (choiceID, num) VALUES (?, ?)");
		$sqlCreateItem->bind_param(
			"ii",
			$nextChoiceId,
			$num
		);
		$sqlCreateItem->execute();
		$sqlCreateItem->close();
	}

	$choice['hash'] = sha1(serialize($choice['items']));

	if (!in_array($choice['hash'], $chash)) {
		if ($set['type'] == 'integer') {
			$choice['display'] = $choice['items'][0];
			$choice['width'] = strlen($choice['display']) + 2;
		}
		$displayWidth = max($displayWidth, $choice['width']);

		array_push($choices, $choice);
		array_push($chash, $choice['hash']);

		$sqlUpdateChoice = $conn->prepare("UPDATE choices SET valid = TRUE, display = ? WHERE id = ?");
		$sqlUpdateChoice->bind_param(
			"ii",
			$choice['display'],
			$nextChoiceId
		);
		$sqlUpdateChoice->execute();
		$sqlUpdateChoice->close();

		$uniqueChoices++;
	}
}

$sqlUpdateQuestion = $conn->prepare("UPDATE questions SET displayWidth = ? WHERE id = ?");
$sqlUpdateQuestion->bind_param(
	"ii",
	$displayWidth,
	$question['id']
);
$sqlUpdateQuestion->execute();
$sqlUpdateQuestion->close();

$conn->close();

// trigger prediction request

file_get_contents('http://localhost:8000/', false, stream_context_create(array('http' => array(
	'method' => 'POST',
	'header' => 'Content-Type: text/xml',
	'content' => xmlrpc_encode_request('analysisRequest', array($set['id'], $set['setInd']))
))));

$pageName = 'question';
$pageDisplay = 'Question';

require_once('../includes/page.php');