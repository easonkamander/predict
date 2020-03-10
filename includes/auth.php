<?php
require_once('../vendor/autoload.php');

session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/../oauth-credentials.json');
$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/auth_callback.php');
$client->setScopes('openid');