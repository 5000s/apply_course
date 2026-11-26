<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ส่ง SMS ผ่าน SMS-KUB (รูปแบบเดียวกับ CourseApplyController::sendSMS ที่ใช้ส่ง OTP)
 */
class SmsSender
{
    // เบอร์ติดต่อมูลนิธิ (ใช้ลงท้าย SMS ถึงผู้สมัคร)
    const CONTACT_PHONE = '02-117-4063';

    // รับเฉพาะเบอร์มือถือไทย 0[689]XXXXXXXX (แปลง +66/66 ให้)
    public function normalizeThaiMobile(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '66') && strlen($digits) === 11) {
            $digits = '0' . substr($digits, 2);
        }

        return preg_match('/^0[689]\d{8}$/', $digits) ? $digits : null;
    }

    // เบอร์มือถือไทยของสมาชิก (phone ก่อน แล้วค่อย phone_new)
    public function memberMobile(?Member $member): ?string
    {
        if (!$member) {
            return null;
        }

        return $this->normalizeThaiMobile($member->phone) ?? $this->normalizeThaiMobile($member->phone_new);
    }

    public function send(string $phone, string $message): bool
    {
        $url    = config('services.smskub.url');
        $apiKey = config('services.smskub.key');
        $from   = config('services.smskub.sender', 'KBO staff');

        if (!$url || !$apiKey) {
            Log::warning("SMSKUB not configured. Skip SMS to {$phone}");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'key'          => $apiKey,
            ])
                ->timeout(10)
                ->send('POST', $url, [
                    'body' => json_encode(['to' => [$phone], 'from' => $from, 'message' => $message], JSON_UNESCAPED_UNICODE),
                ]);
        } catch (\Throwable $e) {
            Log::error('SMSKUB request failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            return false;
        }

        if (!$response->successful()) {
            Log::error('SMSKUB send error', ['phone' => $phone, 'status' => $response->status(), 'body' => $response->body()]);
            return false;
        }

        return true;
    }
}
