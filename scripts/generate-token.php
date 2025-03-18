<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Client;

$client = new Client();
$client->setAuthConfig(__DIR__ . '/../storage/app/google/oauth-credentials.json');
$client->setRedirectUri('http://localhost');
$client->setScopes(['https://www.googleapis.com/auth/gmail.send']);
$client->setAccessType('offline');
$client->setPrompt('consent');

// ✅ Disable SSL verification for local development
$client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));

echo "Open the following link in your browser:\n";
echo $client->createAuthUrl() . "\n";
echo "Enter the authorization code: ";
$authCode = trim(fgets(STDIN));

$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
file_put_contents(__DIR__ . '/../storage/app/google/token.json', json_encode($accessToken, JSON_PRETTY_PRINT));

echo "✅ Token saved successfully!\n";
