<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GmailService;

class GmailController extends Controller
{
    public function sendTestEmail()
    {
        $gmailService = new GmailService();
        $response = $gmailService->sendEmail('arttioz@gmail.com', 'Test Email', 'Hello! This is a test email from Laravel using Gmail API.');

        return response()->json(['message' => $response]);
    }
}
