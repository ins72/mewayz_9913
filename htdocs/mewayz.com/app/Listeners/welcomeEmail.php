<?php

namespace App\Listeners;

use App\Yena\YenaMail;
use Illuminate\Auth\Events\Registered;

class welcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){
        //
    }

    public function handle($event){
        // Get user from event
        $user = $event->user;

        $mail = new YenaMail;
        $mail->send([
           'to' => $user->email,
           'subject' => __('Welcome Aboard :name 👋', ['name' => $user->name]),
        ], 'account.welcome', [
           'user' => $user
        ]);
    }
}
