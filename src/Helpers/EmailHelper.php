<?php

namespace App\Helpers;

use \App\Services\EmailProvider as Provider;
use \App\Services\EmailSMime as SMime;

class EmailHelper {

    public static function buildProvider() : Provider {

        $Host = $_SERVER['EMAIL_SERVICE_HOST'];
        $Port = $_SERVER['EMAIL_SERVICE_PORT'];
        $User = !empty($_SERVER['EMAIL_SERVICE_USER']) ? $_SERVER['EMAIL_SERVICE_USER'] : null;
        $Password = !empty($_SERVER['EMAIL_SERVICE_PASSWORD']) ? $_SERVER['EMAIL_SERVICE_PASSWORD'] : null;
        return new Provider($Host, $Port, $User, $Password);

    }

    public static function buildSMime() : ?SMime {

        $Environment = 'DEV';

        if($Environment == 'DEV' || $Environment == 'CLI' )
            return null;

        $CertificatePath = '';
        $KeyPath= '';
        $KeyPassword= '';

        return new SMime($CertificatePath, $KeyPath, $KeyPassword);

    }

}