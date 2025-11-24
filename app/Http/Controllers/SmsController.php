<?php

// app/Http/Controllers/SmsController.php
namespace App\Http\Controllers;

use App\Services\SmsKubClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SmsController extends Controller
{
    public function form()
    {
        return view('sms.send');
    }

    public function send(Request $request, SmsKubClient $client)
    {
        $data = $request->validate([
            'to'      => ['required','string','max:20'],   // ปรับ rule ได้ตามเบอร์ E.164
            'message' => ['required','string','max:1000'],
            'sender'  => ['nullable','string','max:20'],
        ]);

        $result = $client->send($data['to'], $data['message'], $data['sender'] ?? null);

        return back()->with([
            'sms_result' => $result,
            'old_input'  => $data,
        ]);
    }


    public function sendSms(Request $request)
    {
        // 1. Validate the incoming request data
        try {
            $validatedData = $request->validate([
                'to' => 'required|string|regex:/^[0-9]{10,12}$/', // Basic validation for a phone number
                'message' => 'required|string|max:1000', // Max length for SMS
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation Failed', 'messages' => $e->errors()], 422);
        }

        // 2. Get API Key and Sender ID from .env file
        //    IMPORTANT: Add these to your .env file!
        //    TWOFACTOR_API_KEY=your_api_key_here
        //    TWOFACTOR_SENDER_ID=your_sender_id_here (e.g., "market")
        $apiKey = env('SMSKUB_API_KEY');
        $senderId = "env('KBO staff')";

        // Check if config is missing
        if (!$apiKey || !$senderId) {
            return response()->json(['error' => 'SMS configuration is missing. Please set TWOFACTOR_API_KEY and TWOFACTOR_SENDER_ID in your .env file.'], 500);
        }

        // 3. Construct the API URL
        $url = "https://2factor.in/API/V1/{$apiKey}/ADDON_SERVICES/SEND/TSMS";

        // 4. Prepare the form data as per the documentation
        $formData = [
            'From' => $senderId,
            'To' => $validatedData['to'],
            'Msg' => $validatedData['message'],
        ];

        // 5. Make the POST request using Laravel's Http facade
        //    We use asForm() because the API expects 'form-data'
        $response = Http::asForm()->post($url, $formData);

        // 6. Handle the response from the API
        if ($response->successful()) {
            // API call was successful (e.g., 2xx status code)
            $apiResult = $response->json(); // Get the JSON response body

            if (isset($apiResult['Status']) && $apiResult['Status'] === 'Success') {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully.',
                    'details' => $apiResult['Details'] ?? null
                ], 200);
            } else {
                // The API returned a "Success" status but with an error message
                return response()->json([
                    'success' => false,
                    'message' => 'API returned an error.',
                    'api_response' => $apiResult
                ], 400);
            }

        } else {
            // API call failed (e.g., 4xx or 5xx status code)
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS due to an API error.',
                'status_code' => $response->status(),
                'api_response' => $response->body() // Get raw body for debugging
            ], $response->status());
        }
    }
}
