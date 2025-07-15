<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use DirectoryTree\Bartender\Facades\Bartender;
use App\Socialite\UserProviderHandler;
use App\YenaOauth\Facades\YenaOauth;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        YenaOauth::serve('google', UserProviderHandler::class);
        YenaOauth::serve('facebook', UserProviderHandler::class);

        
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(__('Verify Email Address'))->view(
                    'email.templates.account.verify', [
                        'url' => $url
                    ]
                );
        });
    }
}
