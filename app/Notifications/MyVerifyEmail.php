<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\GmailApiService; // Your custom service
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Needed for expiration
use Illuminate\Support\Facades\URL; // Needed to create signed URL

class MyVerifyEmail extends Notification implements ShouldQueue // Make it queueable
{
    use Queueable;

    public static $createUrlCallback; // For customization if needed
    public static $toMailCallback; // For customization if needed

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        // This creates the signed URL for email verification
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)), // Expiry time
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function via($notifiable)
    {
        return ['mail']; // We still use the 'mail' channel, but custom handle it
    }

    public function toMail($notifiable)
    {
        // If you have a custom callback set for toMail, use it
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->verificationUrl($notifiable));
        }

        $locale = $notifiable->locale
            ?? $notifiable->language
            ?? app()->getLocale();

        $verifyUrl = $this->verificationUrl($notifiable);


        $subject = Lang::get('mail.verify.subject', [] ,$locale);


        $prevLocale = app()->getLocale();
        App::setLocale($locale);

        $template = 'emails.verify_email';

        $data = [
            'name' => $notifiable->name ?? $notifiable->first_name, // Use first_name if available
            'verification_url' => $verifyUrl,
            'app_name' => config('app.name'),
        ];

        try {
            $gmailService = new GmailApiService();
            $gmailService->sendEmail($notifiable->email, $subject, $template, $data);
            // We don't return a MailMessage here because we're handling the send directly.
            // If you want Laravel to think it sent a Mailable for logging/etc.,
            // you could return a dummy MailMessage, but it won't be sent by Laravel's mailer.
        } catch (\Exception $e) {
            Log::error('Failed to send email verification via Gmail API for ' . $notifiable->email . ': ' . $e->getMessage());
            // Handle the error appropriately, e.g., retry the job or notify an admin.
            throw $e; // Re-throw to mark job as failed/retry.
        }

        return (new MailMessage)
            ->mailer('log')
            ->line('This is a dummy email from the notification system.')
            ->line('The actual email was sent via GmailApiService.')
            ->view('emails.dummy_email_template'); // Use your dummy template

    }

    // Add a view for your verification email: resources/views/emails/verify_email.blade.php
    // This view will receive $name, $verification_url, $app_name
}
