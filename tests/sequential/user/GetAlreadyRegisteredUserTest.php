<?php

declare(strict_types=1);

use TestDependencies\HTTP\HTTPResponseTest;
use App\Constants\UsersConstants;

final class GetAlreadyRegisteredUserTest extends HTTPResponseTest {

    public static function setUpBeforeClass(): void {

        $DefaultRootCredentials = UsersConstants::getDefaultRootData();
        $Cookie = self::login($DefaultRootCredentials['Email'], $DefaultRootCredentials['Password']);

        $Curl = curl_init();
        $Url = self::getApiBase()."/user";
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($Curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Cookie: ".$Cookie]);
        self::$Response = curl_exec($Curl);
        self::$HttpCode = curl_getinfo($Curl, CURLINFO_HTTP_CODE);

        self::logout($Cookie);

    }

    public function testResponseHttpCodeIs200(): void {

        $this->assertSame(self::$HttpCode, 200);

    }

    public function testResponseDefaultExpectedStructure(): void {

        $DefaultRootCredentials = UsersConstants::getDefaultRootData();
        $ResponseArray = json_decode(self::$Response,true);
        $this->assertFalse(isset($ResponseArray['error']));
        $this->assertTrue(isset($ResponseArray['result']['ID']));
        $this->assertTrue(isset($ResponseArray['result']['Name']));
        $this->assertSame($ResponseArray['result']['Name'], $DefaultRootCredentials['Name']);
        $this->assertTrue(isset($ResponseArray['result']['Identifier']));
        $this->assertSame($ResponseArray['result']['Identifier'], $DefaultRootCredentials['Identifier']);
        $this->assertTrue(isset($ResponseArray['result']['Email']));
        $this->assertSame($ResponseArray['result']['Email'], $DefaultRootCredentials['Email']);
        $this->assertTrue(isset($ResponseArray['result']['Type']));
        $this->assertTrue(isset($ResponseArray['result']['CustomerServerID']));

    }

}
