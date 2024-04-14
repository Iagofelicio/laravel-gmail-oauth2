<?php

namespace Iagofelicio\LaravelGmailOauth2\Providers;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;
use Iagofelicio\LaravelGmailOauth2\Transport\GmailTransport;

class GmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend(MailManager::class, function ($manager, $app) {
            $manager->extend('gmail', function ($app) {
                return new GmailTransport();
            });
            return $manager;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
