<?php

declare(strict_types=1);

use TestDependencies\HTTP\HTTPResponseTest;
use App\Constants\UsersConstants;

final class AddCommonInformationExchangeTradedAssetsNoAssetTest extends HTTPResponseTest {

    protected static ?string $Cookie;

    public static function setUpBeforeClass(): void {

        $DefaultRootCredentials = UsersConstants::getDefaultRootData();
        self::$Cookie = self::login($DefaultRootCredentials['Email'], $DefaultRootCredentials['Password']);

        $Curl = curl_init();
        $Url = self::getApiBase()."/common-information/exchange-traded-assets";
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Cookie: ".self::$Cookie]);
        curl_setopt($Curl, CURLOPT_POST, true);
        curl_setopt($Curl, CURLOPT_POSTFIELDS, json_encode([]));
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
        self::$Response = curl_exec($Curl);
        self::$HttpCode = curl_getinfo($Curl, CURLINFO_HTTP_CODE);

        self::logout(self::$Cookie);

    }

    public function testResponseHttpCodeIs200(): void {

        $this->assertSame(self::$HttpCode, 200);

    }

    public function testResponseDefaultExpectedMessage(): void {

        $ResponseArray = json_decode(self::$Response, true);
        $this->assertFalse(isset($ResponseArray['error']));
        $this->assertSame($ResponseArray['result'], []);

    }

}
