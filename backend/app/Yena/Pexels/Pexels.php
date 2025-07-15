<?php

namespace App\Yena\Pexels;

use App\Yena\Pexels\Clients\PhotoApiClient;

class Pexels
{
    /**
     * Return Pexels photo api client
     * @return PhotoApiClient
     */
    public function photos()
    {
        return new PhotoApiClient();
    }
}
