<?php

namespace App\Helpers;

use App\Services\Random;
use App\Services\Log;

class ResetPasswordRequestHelper {

    private const StandardDuration = 1800; //30 mins


    public static function generateToken() : string {

        return Random::generateToken();

    }

    public static function getStandardDuration() : int {

        return self::StandardDuration;

    }

    public static function sendResponse(string $Token) : bool {

        Log::add('DEBUG', 'reset-password-request-token', $Token);
        return true;

    }

}