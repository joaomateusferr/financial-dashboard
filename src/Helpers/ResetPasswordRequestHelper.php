<?php

namespace App\Helpers;

use App\Services\Random;
use App\Services\Log;

class ResetPasswordRequestHelper {

    private const StandardDuration = 600; //10 mins


    public static function generateToken() : string {

        return Random::generateToken();

    }

    public static function getStandardDuration() : int {

        return self::StandardDuration;

    }

    public static function sendResponse(string $Token) : bool {

        $Url = $_SERVER['API_BASE_URL'].'/reset-password/'.$Token;
        Log::add('DEBUG', 'reset-password-request-token', $Url);
        return true;

    }

}