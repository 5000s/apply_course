<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;
// Remove: use Illuminate\Support\Facades\Mail; // You don't need this facade here for rendering

// Add this if you want to use the view() helper function or the View facade
use Illuminate\Support\Facades\View; // Or simply use the global view() helper

class GmailApiService
{
    protected $client;
    protected $service;
    protected $impersonateEmail;

    public function __construct()
    {
        $this->client = new Client();
        $path = storage_path(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $this->client->setAuthConfig($path);
        Log::info("Google Auth Config Path: " . $path);

        $this->client->setApplicationName(config('app.name'));

        $scopes = explode(' ', env('GOOGLE_AUTH_SCOPES'));
        $this->client->setScopes($scopes);

        $this->impersonateEmail = env('GOOGLE_IMPERSONATE_EMAIL');
        $this->client->setSubject($this->impersonateEmail);

        $this->service = new Gmail($this->client);
    }

    public function sendEmail($to, $subject, $view, $data, $from = null)
    {
        $fromEmail = $from ?: $this->impersonateEmail;

        try {
            // FIX IS HERE: Render the Blade view directly using the view() helper or View facade
            // Option 1: Using the global view() helper function (most common)
            $body = view($view, $data)->render();

            // Option 2: Using the View facade
            // $body = View::make($view, $data)->render();


            // 2. Build the raw MIME message string
            $str = "From: {$fromEmail}\r\n";
            $str .= "To: {$to}\r\n";
            $str .= "Subject: =?utf-8?B?" . base64_encode($subject) . "?=\r\n";
            $str .= "MIME-Version: 1.0\r\n";
            $str .= "Content-Type: text/html; charset=utf-8\r\n";
            $str .= "Content-Transfer-Encoding: base64\r\n\r\n"; // Removed the base64_encode here as the API expects raw content to encode
            $str .= $body; // This is the HTML content

            // The Gmail API expects the entire raw email string (including headers and body)
            // to be base64url encoded.
            $base64EncodedEmail = rtrim(strtr(base64_encode($str), '+/', '-_'), '=');

            // 3. Create a Gmail Message object
            $message = new Message();
            $message->setRaw($base64EncodedEmail);

            // 4. Send the message using the Gmail API
            $this->service->users_messages->send('me', $message);

            Log::info("Email sent successfully to {$to} from {$fromEmail} via Gmail API.");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$to} from {$fromEmail} via Gmail API: " . $e->getMessage());
            throw $e;
        }
    }
}
