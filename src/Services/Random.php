<?php

namespace App\Services;

class Random {

    public static function generateToken() : string {

        return bin2hex(random_bytes(32));

    }

}