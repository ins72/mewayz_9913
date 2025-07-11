<?php

namespace App\Yena\Pexels\Facades;

use App\Yena\Pexels\Clients\PhotoApiClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method PhotoApiClient photos()
 */
class Pexels extends Facade
{
    /**
     * Return facade unique key
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pexels';
    }
}
