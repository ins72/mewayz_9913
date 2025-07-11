<?php

namespace App\Yena\Pexels\Exceptions;

use Exception;
use App\Yena\Pexels\Photo;

class NoPexelsResultsReturned extends Exception
{
    public function __construct(?string $code = null)
    {
        parent::__construct(
            "No results returned. Pexels API code: $code"
        );
    }
}
