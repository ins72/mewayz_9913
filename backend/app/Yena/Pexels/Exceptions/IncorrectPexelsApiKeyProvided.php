<?php

namespace App\Yena\Pexels\Exceptions;

use Exception;
use App\Yena\Pexels\Photo;

class IncorrectPexelsApiKeyProvided extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Unauthorized. Please check your PEXELS_API_KEY is correct or get a new one. To get a key visit: https://www.pexels.com/api/new/'
        );
    }
}
