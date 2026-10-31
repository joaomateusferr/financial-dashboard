<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ApiBase;
use App\Repositories\UserRepository;
use App\Repositories\ResetPasswordRequestRepository;
use App\Helpers\ResetPasswordRequestHelper;
use App\Helpers\DatabaseHelper;

class ResetPasswordController extends ApiBase {

    public function create(Request $Request, Response $Response) {

        $Data = self::formatBody($Request->getBody()->getContents());

        if(!isset($Data['Email']))
            return self::buildResponse($Response, ['Email field is mandatory!'], 400, true);

        if(!filter_var($Data['Email'], FILTER_VALIDATE_EMAIL))
            return self::buildResponse($Response, ['Invalid email!'], 400, true);

        DatabaseHelper::connect('kernel', 'kernel');

        $UserDetails = UserRepository::retrieveUserDetailsByEmail($Data['Email']);

        if(is_null($UserDetails))
            return self::buildResponse($Response, ['Unable to fetch data!'], 500, true);

        $Token = ResetPasswordRequestRepository::create($UserDetails['ID']);

        DatabaseHelper::disconnect('kernel');

        if(empty($Token))
            return self::buildResponse($Response, ['Unable to start a password reset!'], 500, true);

        $Result = ResetPasswordRequestHelper::sendResponse($Token);

        if(empty($Result))
            return self::buildResponse($Response, ['Unable to send a password reset response!'], 500, true);

        return self::buildResponse($Response, ['Password reset process initiated!']);

    }

    public function get(Request $Request, Response $Response) {

        /*DatabaseHelper::connect('kernel', 'kernel');
        $UserData = UserRepository::retrieveUserDetailsBySID($_COOKIE['sid']);
        DatabaseHelper::disconnect('kernel');
        return self::buildResponse($Response, $UserData);*/

    }

}