<?php

namespace App\UiControllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UiBase;

class HomeController extends UiBase {

    public function logs(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'logs.php');

    }

    public function logsList(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'logs-list.php');

    }

    public function signin(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'signin.php');

    }

    public function home(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'home.php');

    }

    public function login(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'login.php');

    }

    public function startResetPassword(Request $Request, Response $Response) {

        return self::buildResponse($Response, 'start-reset-password.php');

    }

    public function resetPassword(Request $Request, Response $Response, array $Args) {

        $ID = !empty($Args['ID']) ? $Args['ID'] : '';
        return self::buildResponse($Response, 'reset-password.php', ['ID' => $ID]);

    }

}