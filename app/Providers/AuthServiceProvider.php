<?php

namespace App\Providers;

use App\Notifications\MyResetPassword;
use App\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // This tells Laravel to use your custom MyResetPassword notification
        // when the password reset process is initiated.
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return URL::route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false); // Use false for relative URL path if appropriate for your app
        });

        // This is the key part: tell Laravel to use your custom notification
        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            // Instantiate your custom notification and call its toMail method
            return (new MyResetPassword($token))->toMail($notifiable);
        });
    }
}
