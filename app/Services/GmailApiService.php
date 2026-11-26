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

    /**
     * @param array<string, string> $inlineImages รูปที่ฝังในอีเมล [content-id => path ไฟล์] อ้างอิงใน HTML ด้วย src="cid:content-id"
     */
    public function sendEmail($to, $subject, $view, $data, $from = null, array $inlineImages = [])
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

            if ($inlineImages) {
                $str .= $this->buildRelatedMimeBody($body, $inlineImages);
            } else {
                $str .= "Content-Type: text/html; charset=utf-8\r\n";
                $str .= "Content-Transfer-Encoding: base64\r\n\r\n"; // Removed the base64_encode here as the API expects raw content to encode
                $str .= $body; // This is the HTML content
            }

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

    /**
     * multipart/related: ส่วน HTML + รูปฝังในอีเมล (Content-ID) ให้เห็นรูปได้ทันทีโดยไม่ต้องโหลดจากเว็บ
     * รูปที่หาไฟล์ไม่เจอจะถูกข้าม (บันทึก log) ไม่ทำให้การส่งอีเมลล้มเหลว
     *
     * @param array<string, string> $inlineImages [content-id => path ไฟล์]
     */
    private function buildRelatedMimeBody(string $html, array $inlineImages): string
    {
        $boundary = 'related_' . bin2hex(random_bytes(12));

        $mime = "Content-Type: multipart/related; boundary=\"{$boundary}\"\r\n\r\n";
        $mime .= "--{$boundary}\r\n";
        $mime .= "Content-Type: text/html; charset=utf-8\r\n";
        $mime .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $mime .= chunk_split(base64_encode($html)) . "\r\n";

        foreach ($inlineImages as $contentId => $path) {
            if (!is_file($path) || !is_readable($path)) {
                Log::warning("Inline email image not found, skipped: {$path}");
                continue;
            }

            $fileName = basename($path);
            $mimeType = mime_content_type($path) ?: 'application/octet-stream';

            $mime .= "--{$boundary}\r\n";
            $mime .= "Content-Type: {$mimeType}; name=\"{$fileName}\"\r\n";
            $mime .= "Content-Transfer-Encoding: base64\r\n";
            $mime .= "Content-ID: <{$contentId}>\r\n";
            $mime .= "Content-Disposition: inline; filename=\"{$fileName}\"\r\n\r\n";
            $mime .= chunk_split(base64_encode(file_get_contents($path))) . "\r\n";
        }

        $mime .= "--{$boundary}--\r\n";

        return $mime;
    }
}
