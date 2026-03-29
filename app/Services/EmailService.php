<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected $client;
    protected $service;
    protected $impersonateEmail;

    public function __construct()
    {
        $this->client = new Client();
        
        // Define paths to check for credentials file
        $credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS');
        $path = storage_path($credentialsPath);
        
        // Fallback to base path if not found in storage
        if (!file_exists($path)) {
            $path = base_path($credentialsPath);
        }
        
        $this->client->setAuthConfig($path);
        
        $this->client->setApplicationName(env('MAIL_FROM_NAME', config('app.name')));

        $scopes = explode(' ', env('GOOGLE_AUTH_SCOPES', 'https://www.googleapis.com/auth/gmail.send'));
        $this->client->setScopes($scopes);

        // Required setting for domain wide delegation/service accounts sending mail
        $this->impersonateEmail = env('GOOGLE_IMPERSONATE_EMAIL');
        if ($this->impersonateEmail) {
            $this->client->setSubject($this->impersonateEmail);
        }

        $this->service = new Gmail($this->client);
    }

    /**
     * Send email with raw HTML content to a specific user
     *
     * @param string $HTML The HTML content to include in the email
     * @param string $emailTo The recipient's email address
     * @param string $subject The email subject
     * @return bool
     */
    public function sendemail($HTML, $emailTo, $subject = 'Notification')
    {
        $fromEmail = env('MAIL_FROM_ADDRESS');
        $fromName = env('MAIL_FROM_NAME');
        
        try {
            // Encode the HTML body in base64 as required for MIME payload
            $bodyEncoded = rtrim(chunk_split(base64_encode($HTML), 76, "\r\n"));

            // Build the raw MIME message string
            $str  = "From: =?utf-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>\r\n";
            $str .= "To: {$emailTo}\r\n";
            $str .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
            $str .= "MIME-Version: 1.0\r\n";
            $str .= "Content-Type: text/html; charset=utf-8\r\n";
            $str .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $str .= $bodyEncoded;

            // The Gmail API expects the entire raw email string (including headers and body)
            // to be base64url encoded.
            $base64EncodedEmail = rtrim(strtr(base64_encode($str), '+/', '-_'), '=');

            // Create a Gmail Message object
            $message = new Message();
            $message->setRaw($base64EncodedEmail);

            // Send the message using the Gmail API
            $this->service->users_messages->send('me', $message);

            Log::info("Email sent successfully to {$emailTo} via EmailService.");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$emailTo} via EmailService: " . $e->getMessage());
            throw $e;
        }
    }
}
