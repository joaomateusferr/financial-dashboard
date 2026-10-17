<?php

namespace App\UiControllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UiBase;
use App\Services\Password;
use App\Helpers\APIRequestHelper;
use App\Constants\DashboardTabsConstants;

class OperationController extends UiBase {

    public function signin(Request $Request, Response $Response) {

        $Data = empty($_POST) ? [] : $_POST;

        $DefaultErrorTitle = 'Error!';
        $DefaultErrorFooter = 'Do you want to try again? <a href="/signin">Signin</a>';

        if(empty($Data['Email']))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The email field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        $Data['Email'] = trim($Data['Email']);

        if(!filter_var($Data['Email'], FILTER_VALIDATE_EMAIL))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The email field must contain a valid email address!', 'Footer' => $DefaultErrorFooter]);

        if(empty($Data['EmailConfirmation']))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The email confirmation field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        $Data['EmailConfirmation'] = trim($Data['EmailConfirmation']);

        if($Data['Email'] != $Data['EmailConfirmation'])
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => "The emails don't match!", 'Footer' => $DefaultErrorFooter]);

        if(empty($Data['Password']))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The password field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        $PasswordMinimumPasswordSecurityResult = Password::validateMinimumPasswordSecurity($Data['Password']);

        if(!empty($PasswordMinimumPasswordSecurityResult))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => $PasswordMinimumPasswordSecurityResult[0], 'Footer' => $DefaultErrorFooter]);

        if(empty($Data['PasswordConfirmation']))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The password confirmation field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        if($Data['Password'] != $Data['PasswordConfirmation'])
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => "The passwords don't match!", 'Footer' => $DefaultErrorFooter]);

        if(empty($Data['Terms']))
            return self::buildResponse($Response, 'signin-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'Accepting the terms is mandatory!', 'Footer' => $DefaultErrorFooter]);

        $Result = APIRequestHelper::sendRequest($_SERVER['HTTP_USER_AGENT'], 'POST','/user',['Email' => $Data['Email'], 'Password' => $Data['Password']],["Content-type: application/json"]);

        if(empty($Result))
            $Result = ['error' => true, 'result' => ['Request issue!']];

        $Title = !empty($Result['error'])  ? 'Error:' : 'Success:';
        $Description = !empty($Result['result']) ? $Result['result'][0] : '';
        $Footer = !empty($Result['error'])  ? $DefaultErrorFooter : 'Have you activated your account yet? <a href="/login">Login</a>';

        return self::buildResponse($Response, 'signin-result.php', ['Title' => $Title, 'Description' => $Description, 'Footer' => $Footer]);

    }

    public function login(Request $Request, Response $Response) {

        $Data = empty($_POST) ? [] : $_POST;

        $DefaultErrorTitle = 'Error!';
        $DefaultErrorFooter = 'Do you want to try again? <a href="/login">Login</a>';

        if(isset($Data['Remember']))
            $Data['Remember'] = (bool) $Data['Remember'];

        $Data['Email'] = trim($Data['Email']);

        if(empty($Data['Email']))
            return self::buildResponse($Response, 'login-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The email field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        if(!filter_var($Data['Email'], FILTER_VALIDATE_EMAIL))
            return self::buildResponse($Response, 'login-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The email field must contain a valid email address!', 'Footer' => $DefaultErrorFooter]);

        if(empty($Data['Password']))
            return self::buildResponse($Response, 'login-result.php', ['Title' => $DefaultErrorTitle, 'Description' => 'The password field is mandatory!', 'Footer' => $DefaultErrorFooter]);

        $PasswordMinimumPasswordSecurityResult = Password::validateMinimumPasswordSecurity($Data['Password']);

        if(!empty($PasswordMinimumPasswordSecurityResult))
            return self::buildResponse($Response, 'login-result.php', ['Title' => $DefaultErrorTitle, 'Description' => $PasswordMinimumPasswordSecurityResult[0], 'Footer' => $DefaultErrorFooter]);

        $Result = APIRequestHelper::sendRequest($_SERVER['HTTP_USER_AGENT'], 'POST','/session',['Email' => $Data['Email'], 'Password' => $Data['Password']],["Content-type: application/json"]);

        if($Result === false)
            $Result = ['error' => true, 'result' => ['Request issue!']];

        $DashboardTabsConstants = DashboardTabsConstants::getDashboardTabs();

        $Title = !empty($Result['error'])  ? 'Error:' : 'Success:';
        $Description = !empty($Result['result']) ? $Result['result'][0] : '';
        $Footer = !empty($Result['error'])  ? 'Do you want to try again? <a href="/login">Login</a>' : '';
        $Redirect = !empty($Result['error']) ? '' : '/dashboard/'.array_key_first($DashboardTabsConstants);

        if(empty($Result['error'])){

            $SetCookie = APIRequestHelper::parseSetCookie($Result['Headers']['Set-Cookie']);

            session_set_cookie_params($SetCookie['Max-Age']);
            session_start();
            $_SESSION['SID'] = $SetCookie['sid'];
            session_regenerate_id(true);

            $Result = APIRequestHelper::sendRequest($_SERVER['HTTP_USER_AGENT'], 'GET','/user',[],["Cookie: sid=".$SetCookie['sid'],"Content-type: application/json"]);

            if($Result === false)
                $Result = ['error' => true, 'result' => ['Request issue!']];
            else
                $Result = $Result['result'];

            if(!empty($Result['error'])){

                $Title = 'Error';
                $Description = !empty($Result['result']) ? $Result['result'][0] : '';
                $Redirect = '';

            }

            $_SESSION['UserID'] = $Result['ID'];
            $_SESSION['UserName'] = $Result['Name'];
            $_SESSION['UserIdentifier'] = $Result['Identifier'];
            $_SESSION['UserEmail'] = $Result['Email'];
            $_SESSION['UserType'] = $Result['Type'];
            $_SESSION['CustomerServerID'] = $Result['CustomerServerID'];
            $_SESSION['ExpiresIn'] = time() + $SetCookie['Max-Age'];

        }

        return self::buildResponse($Response, 'login-result.php', ['Title' => $Title, 'Description' => $Description, 'Footer' => $Footer, 'Redirect' => $Redirect]);

    }

    public function logout(Request $Request, Response $Response) {

        session_start();

        $Result = APIRequestHelper::sendRequest($_SERVER['HTTP_USER_AGENT'], 'DELETE','/session',[],["Cookie: sid=".$_SESSION['SID'],"Content-type: application/json"]);

        if($Result === false)
            $Result = ['error' => true, 'result' => ['Request issue!']];

        session_destroy();

        return self::buildResponse($Response, 'redirect.php', ['Redirect' => '/login']);

    }

}