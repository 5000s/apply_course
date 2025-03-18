<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use App\Mail\GmailTransport;

class GmailMailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make(MailManager::class)->extend('custom_gmail', function () {
            return new GmailTransport();
        });
    }
}
