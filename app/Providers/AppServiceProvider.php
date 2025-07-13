<?php

namespace App\Providers;

use App\Mail\Auth\EmailVerificationMail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new EmailVerificationMail($notifiable, $url))
                ->subject('Verify Email Address');
                //->line('Click the button below to verify your email address.')
                //->action('Verify Email Address', $url);
        });
    }
}
