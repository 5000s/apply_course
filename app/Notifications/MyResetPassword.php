<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Services\GmailApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL; // Needed for URL::route
use Illuminate\Notifications\Messages\MailMessage;

class MyResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // This is where the URL for the email is generated
        $resetUrl = $this->customResetUrl($notifiable); // <--- CALL YOUR CUSTOM METHOD HERE

        $subject = 'Reset Your Password for ' . config('app.name');
        $template = 'emails.password_reset'; // Ensure this Blade view exists
        $data = [
            'name' => $notifiable->name ?? $notifiable->first_name,
            'reset_url' => $resetUrl,
            'app_name' => config('app.name'),
        ];

        try {
            $gmailService = new GmailApiService();
            $gmailService->sendEmail($notifiable->email, $subject, $template, $data);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email via Gmail API for ' . $notifiable->email . ': ' . $e->getMessage());
            throw $e;
        }

        return (new MailMessage)
            ->mailer('log')
            ->view('emails.dummy_email_template');
    }

    /**
     * Get the password reset URL for the given notifiable.
     * Overrides the default behavior to force absolute URL generation.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function customResetUrl($notifiable) // <--- NEW METHOD TO FORCE ABSOLUTE URL
    {
        return URL::route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], true); // <--- THIS WILL NOW DEFINITIVELY BE TRUE
    }

    // You can remove the static::$createUrlCallback and static::$toMailCallback properties
    // from this custom notification as they are typically for customizing the *default*
    // ResetPassword notification, not your custom one directly.

    // If you explicitly want to remove the 'toArray' method that might be inherited, you can.
    public function toArray($notifiable)
    {
        return [];
    }
}
