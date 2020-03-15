<?php
require_once('../includes/auth.php');

if (!isset($_SESSION['user'])) {
	header('Location: /');
	return;
}

if (!isset($_SESSION['question']) || !isset($_POST['answer']) || !is_numeric($_POST['answer'])) {
	header('Location: question.php');
	return;
}

$_SESSION['set']['ind']++;

$mysqlCredentials = json_decode(file_get_contents('../mysql-credentials.json'), true);

$conn = mysqli_connect($mysqlCredentials["host"], $mysqlCredentials["user"], $mysqlCredentials["password"], $mysqlCredentials["database"]);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$sqlUpdateSet = $conn->prepare("UPDATE sets SET setInd = ? WHERE id = ?");
$sqlUpdateSet->bind_param(
	"ii",
	$_SESSION['set']['ind'],
	$_SESSION['set']['id']
);
$sqlUpdateSet->execute();
$sqlUpdateSet->close();

$sqlUpdateQuestion = $conn->prepare("UPDATE questions SET answer = ? WHERE setID = ? AND id = ?");
$sqlUpdateQuestion->bind_param(
	"iii",
	$_POST['answer'],
	$_SESSION['set']['id'],
	$_SESSION['question']['id']
);
$sqlUpdateQuestion->execute();
$sqlUpdateQuestion->close();

$sqlGetPrediction = $conn->prepare("SELECT prediction FROM choices WHERE questionID = ? AND valid");
$sqlGetPrediction->bind_param(
	"i",
	$_SESSION['question']['id']
);
$sqlGetPrediction->execute();

$sqlGetPredictionResult = $sqlGetPrediction->get_result();

$choices = array();

for ($i = 0; $i < count($_SESSION['question']['display']); $i++) {
	array_push($choices, array(
		'display' => $_SESSION['question']['display'][$i],
		'prediction' => $sqlGetPredictionResult->fetch_assoc()['prediction'],
		'actual' => false
	));
}

$choices[$_POST['answer']]['actual'] = true;

$sqlGetPrediction->close();

$conn->close();

$displayWidth = $_SESSION['question']['displayWidth'];

unset($_SESSION['question']);

$setInd = $_SESSION['set']['ind'];
$setLen = $_SESSION['set']['len'];

if ($_SESSION['set']['ind'] == $_SESSION['set']['len']) {
	unset($_SESSION['set']);
}

$pageName = 'answer';
$pageDisplay = 'Answer';

require_once('../includes/page.php');