<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailService;

class EmailTestController extends Controller
{
    public function sendTestEmail()
    {
        $to = 'arttioz@gmail.com'; // Change this to your test email address
        Mail::to($to)->send(new TestEmail());

        return 'Email sent successfully';
    }


    public function testEmail(Request $request)
    {
        $emailService = new EmailService();
        $emailService->sendemail("test", "arttioz@gmail.com", "test");

        return response()->json([
            'status' => 'success',
            'message' => 'Email sent successfully',
        ]);
    }
}
