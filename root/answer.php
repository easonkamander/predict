<?php
require_once('../includes/auth.php');

if (!isset($_SESSION['user'])) {
	header('Location: /');
	return;
}

if (!isset($question) || !isset($_POST['answer']) || !is_numeric($_POST['answer'])) {
	header('Location: question.php');
	return;
}

$set['ind']++;

$mysqlCredentials = json_decode(file_get_contents('../mysql-credentials.json'), true);

$conn = mysqli_connect($mysqlCredentials["host"], $mysqlCredentials["user"], $mysqlCredentials["password"], $mysqlCredentials["database"]);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$sqlUpdateSet = $conn->prepare("UPDATE sets SET setInd = ? WHERE id = ?");
$sqlUpdateSet->bind_param(
	"ii",
	$set['ind'],
	$set['id']
);
$sqlUpdateSet->execute();
$sqlUpdateSet->close();

$sqlUpdateQuestion = $conn->prepare("UPDATE questions SET answer = ? WHERE setID = ? AND id = ?");
$sqlUpdateQuestion->bind_param(
	"iii",
	$_POST['answer'],
	$set['id'],
	$question['id']
);
$sqlUpdateQuestion->execute();
$sqlUpdateQuestion->close();

$sqlGetPrediction = $conn->prepare("SELECT prediction FROM choices WHERE questionID = ? AND valid");
$sqlGetPrediction->bind_param(
	"i",
	$question['id']
);
$sqlGetPrediction->execute();

$sqlGetPredictionResult = $sqlGetPrediction->get_result();

$choices = array();

for ($i = 0; $i < count($question['display']); $i++) {
	array_push($choices, array(
		'display' => $question['display'][$i],
		'prediction' => $sqlGetPredictionResult->fetch_assoc()['prediction'],
		'actual' => false
	));
}

$choices[$_POST['answer']]['actual'] = true;

$sqlGetPrediction->close();

$conn->close();

$displayWidth = $question['displayWidth'];

unset($question);

$setInd = $set['ind'];
$setLen = $set['len'];

if ($set['ind'] == $set['len']) {
	unset($set);
}

$pageName = 'answer';
$pageDisplay = 'Answer';

require_once('../includes/page.php');