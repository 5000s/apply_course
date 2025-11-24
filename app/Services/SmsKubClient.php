<?php
// app/Services/SmsKubClient.php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SmsKubClient
{
    protected Client $http;
    protected string $baseUrl;
    protected string $apiKey;
    protected string $authHeader;
    protected string $authPrefix;
    protected string $sendPath;

    public function __construct()
    {
        $cfg = config('services.smskub');

        $this->baseUrl    = rtrim($cfg['url'], '/');
        $this->apiKey     = $cfg['api_key'];
        $this->authHeader = $cfg['auth_header'] ?: 'Authorization';
        $this->authPrefix = trim($cfg['auth_prefix'] ?? '');
        $this->sendPath   = $cfg['send_path'] ?? '/quick-message';

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 15,
        ]);
    }

    /**
     * ปรับ payload ให้ตรงกับ “ชื่อ field” ในเอกสาร Postman ของคุณ
     * ด้านล่างนี้คือรูปแบบทั่วไปที่เจอบ่อย: { to, message, sender }
     */
    public function send(string $to, string $message, ?string $sender = null, array $extra = []): array
    {
        $headerValue = $this->authPrefix !== ''
            ? $this->authPrefix.' '.$this->apiKey
            : $this->apiKey;

        $headers = [
            $this->authHeader => $headerValue,
            'Accept'          => 'application/json',
            'Content-Type'    => 'application/json',
        ];

        // ---- ปรับชื่อ key ตามเอกสารจริง ----
        // ตัวอย่าง: บางเอกสารใช้ "phone" แทน "to" หรือ "text" แทน "message"
        $payload = array_merge([
            'to'      => $to,       // ถ้า doc ใช้ "phone" → เปลี่ยนเป็น 'phone' => $to
            'message' => $message,  // ถ้า doc ใช้ "text"  → เปลี่ยนเป็น 'text'  => $message
        ], $sender ? ['sender' => $sender] : [], $extra);

        try {
            $res = $this->http->post($this->sendPath, [
                'headers' => $headers,
                'json'    => $payload,
            ]);
            return [
                'ok'       => true,
                'status'   => $res->getStatusCode(),
                'response' => json_decode((string) $res->getBody(), true),
            ];
        } catch (RequestException $e) {
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : null;
            return [
                'ok'       => false,
                'status'   => $e->getCode(),
                'error'    => $e->getMessage(),
                'response' => $body ? json_decode($body, true) : null,
            ];
        }
    }
}

