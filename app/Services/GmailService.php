<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use GuzzleHttp\Client as GuzzleClient; // ✅ Correct namespace

class GmailService
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
        $this->client->setHttpClient(new GuzzleClient(['verify' => false]));

        // Load access token
        $tokenPath = storage_path(config('services.google.token'));
        if (!file_exists($tokenPath) || filesize($tokenPath) === 0) {
            throw new \Exception("❌ token.json is missing or empty! Please re-authenticate.");
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        if (!$accessToken || !isset($accessToken['access_token'])) {
            throw new \Exception("❌ Invalid token format! Regenerate token.json.");
        }

        $this->client->setAccessToken($accessToken);


        if ($this->client->isAccessTokenExpired()) {
            if (!isset($accessToken['refresh_token'])) {
                throw new \Exception("❌ Missing refresh token. Delete token.json and re-authenticate.");
            }

            // 🔍 Log the refresh token for debug
            \Log::info('🔄 Refreshing token...', [
                'refresh_token' => $this->client->getRefreshToken()
            ]);

            $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());

            // 🛑 If refresh fails
            if (isset($newToken['error'])) {
                throw new \Exception("❌ Google API error: " . $newToken['error_description'] ?? $newToken['error']);
            }

            // ✅ Merge old refresh_token (in case it's not included in new token)
            if (!isset($newToken['refresh_token'])) {
                $newToken['refresh_token'] = $accessToken['refresh_token'];
            }

            $newToken['created'] = time();

            file_put_contents($tokenPath, json_encode($newToken, JSON_PRETTY_PRINT));
            $this->client->setAccessToken($newToken);
        }



        $this->service = new Gmail($this->client);
    }


    public function sendEmail($to, $subject, $body)
    {
        $emailMessage = "From: course.manager.techo@gmail.com\r\n";
        $emailMessage .= "To: $to\r\n";
        $emailMessage .= "Subject: $subject\r\n";
        $emailMessage .= "\r\n$body\r\n";

        $rawMessage = base64_encode($emailMessage);
        $message = new Message();
        $message->setRaw($rawMessage);

        try {
            $this->service->users_messages->send('me', $message);
            return "✅ Email sent successfully!";
        } catch (\Exception $e) {
            return "❌ Error: " . $e->getMessage();
        }
    }
}
