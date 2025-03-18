<?php

namespace App\Mail;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use GuzzleHttp\Client as GuzzleClient;
use Swift_Mime_SimpleMessage;
use Illuminate\Mail\Transport\Transport;

class GmailTransport extends Transport
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path(config('services.google.oauth_credentials')));
        $this->client->setScopes(Gmail::GMAIL_SEND);
        $this->client->setAccessType('offline');

        // ✅ Disable SSL verification for local development
        $httpClient = new GuzzleClient(['verify' => false]);
        $this->client->setHttpClient($httpClient);

        // Load token
        $tokenPath = storage_path(config('services.google.token'));
        if (!file_exists($tokenPath) || filesize($tokenPath) === 0) {
            throw new \Exception("❌ token.json is missing or empty! Please re-authenticate.");
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        if (!$accessToken || !isset($accessToken['access_token'])) {
            throw new \Exception("❌ Invalid token format! Regenerate token.json.");
        }

        $this->client->setAccessToken($accessToken);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            if (!isset($accessToken['refresh_token'])) {
                throw new \Exception("❌ Missing refresh token. Delete token.json and re-authenticate.");
            }
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken(), JSON_PRETTY_PRINT));
        }

        $this->service = new Gmail($this->client);
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $rawMessage = base64_encode($message->toString());
        $gmailMessage = new Message();
        $gmailMessage->setRaw($rawMessage);

        try {
            $this->service->users_messages->send('me', $gmailMessage);
        } catch (\Exception $e) {
            throw new \Exception("❌ Gmail API Error: " . $e->getMessage());
        }
    }
}
