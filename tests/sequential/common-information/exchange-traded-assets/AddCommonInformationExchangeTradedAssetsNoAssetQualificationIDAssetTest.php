<?php

declare(strict_types=1);

use TestDependencies\HTTP\HTTPResponseTest;
use App\Constants\UsersConstants;

final class AddCommonInformationExchangeTradedAssetsNoAssetQualificationIDAssetTest extends HTTPResponseTest {

    protected static ?string $Cookie;

    public static function setUpBeforeClass(): void {

        $DefaultRootCredentials = UsersConstants::getDefaultRootData();
        self::$Cookie = self::login($DefaultRootCredentials['Email'], $DefaultRootCredentials['Password']);

        $Curl = curl_init();
        $Url = self::getApiBase()."/common-information/exchange-traded-assets";
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "Cookie: ".self::$Cookie]);
        curl_setopt($Curl, CURLOPT_POST, true);
        curl_setopt($Curl, CURLOPT_POSTFIELDS, json_encode([["Ticker" => "HGLG", "ExchangeID" => 1, "AssetTypeID" => 11, "IsoCode" => "BRL"]]));
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
        self::$Response = curl_exec($Curl);
        self::$HttpCode = curl_getinfo($Curl, CURLINFO_HTTP_CODE);

        self::logout(self::$Cookie);

    }

    public function testResponseHttpCodeIs400(): void {

        $this->assertSame(self::$HttpCode, 400);

    }

    public function testResponseDefaultExpectedMessage(): void {

        $ResponseArray = json_decode(self::$Response, true);
        $this->assertSame($ResponseArray['error'], true);
        $this->assertSame($ResponseArray['result'][0], 'I - 0 - AssetQualificationID is required for assets not denominated in reais!');

    }

}
