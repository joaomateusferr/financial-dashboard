<?php

namespace TestDependencies\HTTP;

require dirname(__DIR__, 3).'/config.php';

use PHPUnit\Framework\TestCase;
use App\Helpers\APIRequestHelper;

abstract class HTTPResponseTest extends TestCase {

    protected static bool | string $Response;
    protected static int $HttpCode;

    protected static function getApiBase(): string {

        require dirname(__DIR__, 3).'/config.php';
        return $_SERVER['API_BASE_URL'].'/api';

    }

    protected static function getUserAgent(): string {

        return 'script';

    }

    public static function login(string $User, string $Password) : ?string {

        $SID = null;

        $Result = APIRequestHelper::sendRequest(self::getUserAgent(), 'POST','/session',['Email' => $User, 'Password' => $Password],["Content-type: application/json"]);

        if(!empty($Result)){

           $SetCookie = APIRequestHelper::parseSetCookie($Result['Headers']['Set-Cookie']);

            if(!empty($SetCookie['sid']))
                $SID = 'sid='.$SetCookie['sid'];

        }

        return $SID;

    }

    public static function logout(string $SID) : bool {

        $Result = APIRequestHelper::sendRequest(self::getUserAgent(), 'DELETE','/session',[],["Cookie: ".$SID,"Content-type: application/json"]);

        if(!empty($Result))
            return true;

        return false;

    }

    public function testResponseIsNotBool(): void {

        $this->assertFalse(is_bool(self::$Response));

    }

    public function testResponseIsString(): void {

        $this->assertTrue(is_string(self::$Response));

    }

    public function testResponseIsJson(): void {

        $this->assertTrue(json_validate(self::$Response));

    }

    public function testResponseValidJsonDecode(): void {

        $this->assertTrue(is_array(json_decode(self::$Response, true)));

    }

}