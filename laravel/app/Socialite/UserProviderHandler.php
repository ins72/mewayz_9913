<?php

namespace App\Socialite;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Provider;
use App\YenaOauth\ProviderHandler;
use App\YenaOauth\ProviderRedirector;
use App\YenaOauth\ProviderRepository;
use Laravel\Socialite\Two\User as SocialiteUser;


class UserProviderHandler implements ProviderHandler
{

    /**
     * Constructor.
     */
    public function __construct(
        protected UserProviderRepository $users,
        protected UserProviderRedirector $redirector,
    ) {
    }

    /**
     * Handle redirecting the user to the OAuth provider.
     */
    public function redirect(Provider $provider, string $driver): RedirectResponse
    {
        // Perform additional logic here...
    
        return $provider->redirect();
    }

    /**
     * Handle an OAuth response from the provider.
     */

     public function callback(Provider $provider, string $driver): RedirectResponse
     {

         try {
             /** @var SocialiteUser $socialite */
             $socialite = $provider->user();
         } catch (Exception $e) {
             return $this->redirector->unableToAuthenticateUser($e, $driver);
         }

         if ($this->users->exists($driver, $socialite)) {
            $user = $this->users->getUser($socialite);
            return $this->redirector->userAuthenticated($user, $socialite, $driver);
         }
 
         try {
             $user = $this->users->updateOrCreate($driver, $socialite);
         } catch (Exception $e) {
            return $this->redirector->unableToCreateUser($e, $socialite, $driver);
         }
 
         return $this->redirector->userAuthenticated($user, $socialite, $driver);
     }
}