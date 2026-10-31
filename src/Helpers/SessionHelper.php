<?php

namespace App\Helpers;

use App\Services\Random;

class SessionHelper {

    private const StandardDuration = 1445; //24 mins and 5 seconds


    public static function generateToken() : string {

        return Random::generateToken();

    }

    public static function getStandardDuration() : int {

        return self::StandardDuration;

    }

}