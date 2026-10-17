<?php

namespace App\Constants;

class UsersConstants {

    private const DefaultType = 'STANDARD';
    private const SupportedTypes = ['STANDARD','ADMIN'];
    private const DefaultRootData = ['Email' => 'root@admin.root','Password' => 'RootPassword!', 'Name' => 'Root'];

    public static function getDefaultType() {

        return self::DefaultType;

    }

    public static function getSupportedTypes() : array {

        return self::SupportedTypes;

    }

    public static function getDefaultRootData() : array {

        return self::DefaultRootData;

    }

}