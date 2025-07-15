<?php

namespace App\YenaOauth;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Two\User as SocialiteUser;

interface ProviderRepository
{
    public function getUser(SocialiteUser $user): Authenticatable;
    /**
     * Determine if the user already exists under a different provider.
     */
    public function exists(string $driver, SocialiteUser $user): bool;

    /**
     * Update or create the socialite user.
     */
    public function updateOrCreate(string $driver, SocialiteUser $user): Authenticatable;
}