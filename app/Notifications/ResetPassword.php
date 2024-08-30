<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends BaseResetPassword
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        return (new MailMessage)
//            ->subject('Your Custom Reset Password Subject')
//            ->greeting('Hello!')
//            ->line('We received a request to reset your password.')
//            ->line('If you made this request, click the button below to reset your password.')
//            ->action('Reset Password', $this->resetUrl($notifiable))
//            ->line('This link will expire in ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' minutes.')
//            ->line('If you did not request a password reset, please ignore this email.');

        $locale = $notifiable->preferred_locale ?? app()->getLocale();

        return (new MailMessage)
            ->subject(Lang::get('mail.reset_password_subject', [], $locale))
            ->line(Lang::get('mail.reset_password_line_1', [], $locale))
            ->action(Lang::get('mail.reset_password_action', [], $locale), $this->resetUrl($notifiable))
            ->line(Lang::get('mail.reset_password_line_2', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')], $locale))
            ->line(Lang::get('mail.reset_password_line_3', [], $locale))
            ->line(Lang::get('mail.reset_password_regards', [], $locale))
            ->line(Lang::get('mail.reset_password_footer', [], $locale));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            // Add any additional data here if needed
        ];
    }
}
