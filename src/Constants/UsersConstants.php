<?php

namespace App\Constants;

class UsersConstants {

    private const DefaultType = 'STANDARD';
    private const SupportedTypes = ['STANDARD','ADMIN'];
    private const DefaultRootCredentials = ['Email' => 'root@financialdashboard.com','Password' => 'RootPassword!', 'Name' => 'Root'];

    public static function getDefaultType() {

        return self::DefaultType;

    }

    public static function getSupportedTypes() : array {

        return self::SupportedTypes;

    }

    public static function getDefaultRootCredentials() : array {

        return self::DefaultRootCredentials;

    }

}