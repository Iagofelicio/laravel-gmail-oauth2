<?php

namespace Iagofelicio\LaravelGmailOauth2\Providers;

use Illuminate\Support\Facades\Mail;
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
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Mail::extend('gmail', function (array $config = []) {
            return new GmailTransport();
        });
    }
}
