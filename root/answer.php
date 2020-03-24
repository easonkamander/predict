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

$mysqlCredentials = json_decode(file_get_contents('../mysql-credentials.json'), true);

$conn = mysqli_connect($mysqlCredentials["host"], $mysqlCredentials["user"], $mysqlCredentials["password"], $mysqlCredentials["database"]);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$sqlGetSet = $conn->prepare("SELECT id, setInd, setLen FROM sets WHERE id = ?");
$sqlGetSet->bind_param(
	"i",
	$_SESSION['set']
);
$sqlGetSet->execute();
$set = $sqlGetSet->get_result()->fetch_assoc();
$sqlGetSet->close();

$set['setInd']++;

$sqlUpdateSet = $conn->prepare("UPDATE sets SET setInd = ? WHERE id = ?");
$sqlUpdateSet->bind_param(
	"ii",
	$set['setInd'],
	$set['id']
);
$sqlUpdateSet->execute();
$sqlUpdateSet->close();

$sqlGetQuestion = $conn->prepare("SELECT id, setInd, choiceLen, itemLen, itemBits, minTime, maxTime, confirmation, displayWidth FROM questions WHERE id = ?");
$sqlGetQuestion->bind_param(
	"i",
	$_SESSION['question']
);
$sqlGetQuestion->execute();
$question = $sqlGetQuestion->get_result()->fetch_assoc();
$sqlGetQuestion->close();

$sqlUpdateQuestion = $conn->prepare("UPDATE questions SET answer = ? WHERE id = ?");
$sqlUpdateQuestion->bind_param(
	"ii",
	$_POST['answer'],
	$question['id']
);
$sqlUpdateQuestion->execute();
$sqlUpdateQuestion->close();

$sqlGetChoices = $conn->prepare("SELECT display, prediction FROM choices WHERE questionID = ? AND valid");
$sqlGetChoices->bind_param(
	"i",
	$question['id']
);
$sqlGetChoices->execute();
$choices = $sqlGetChoices->get_result()->fetch_all(MYSQLI_ASSOC);
$sqlGetChoices->close();

// $choices[$_POST['answer']]['actual'] = true;

$conn->close();

$displayWidth = $question['displayWidth'];

unset($_SESSION['question']);

if ($set['setInd'] == $set['setLen']) {
	unset($_SESSION['set']);
}

$pageName = 'answer';
$pageDisplay = 'Answer';

require_once('../includes/page.php');