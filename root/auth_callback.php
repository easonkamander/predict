<?php
require_once('../includes/auth.php');

if (isset($_SESSION['user'])) {
	if (isset($_GET['logout'])) {
		unset($_SESSION['user']);
		header('Location: /');
		return;
	} elseif ($_SESSION['user']) {
		header('Location: /');
		return;
	} else {
		if (isset($_GET['code'])) {
			$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
			$client->setAccessToken($token);
			$_SESSION['user'] = $client->verifyIdToken()['sub'];

			$mysqlCredentials = json_decode(file_get_contents('../mysql-credentials.json'), true);
			$conn = mysqli_connect("localhost", $mysqlCredentials["user"], $mysqlCredentials["password"], $mysqlCredentials["database"]);
			$sqlUpdateNonAuth = $conn->prepare("UPDATE sets SET user=? WHERE session=? and user IS NULL");
			$sqlUpdateNonAuthSession = session_id();
			$sqlUpdateNonAuth->bind_param(
				"ss",
				$_SESSION['user'],
				$sqlUpdateNonAuthSession
			);
			$sqlUpdateNonAuth->execute();
			$sqlUpdateNonAuth->close();
			$conn->close();

			header('Location: /');
			return;
		} else {
			$auth_url = $client->createAuthUrl();
			header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
			return;
		}
	}
} else {
	if (isset($_GET['nonauth'])) {
		$_SESSION['user'] = "";
		header('Location: question.php');
		return;
	} elseif (isset($_GET['code'])) {
		$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
		$client->setAccessToken($token);
		$_SESSION['user'] = $client->verifyIdToken()['sub'];

		header('Location: /');
		return;
	} else {
		$auth_url = $client->createAuthUrl();
		header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
		return;
	}
}