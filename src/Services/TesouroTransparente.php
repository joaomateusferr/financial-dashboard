<?php

namespace App\Services;

use \DateTime;

class TesouroTransparente {

    private const string ApiUrl = 'https://www.tesourotransparente.gov.br/ckan/api/3/action/package_show?id=taxas-dos-titulos-ofertados-pelo-tesouro-direto';
    private const int RequestTimeout = 10;
    private const int CsvRowsToAnalyze = 201; //200 + Csv Header

    private static function getApiUrl() : string {

        return self::ApiUrl;

    }

    private static function getRequestTimeout() : int {

        return self::RequestTimeout;

    }

    private static function getCsvRowsToAnalyze() : int {

        return self::CsvRowsToAnalyze;

    }

    private static function getResources() : array {

        $Content = file_get_contents(self::getApiUrl(), false, stream_context_create(['http' => ['timeout' => self::getRequestTimeout()]]));

        if(empty($Content))
            return [];

        if(!json_validate($Content))
            return [];

        $Content = json_decode($Content, true);

        if(empty($Content['result']))
            return [];

        return $Content['result'];

    }

    private static function getCsvUrl() : ?string {

        $Data = self::getResources();

        if(empty($Data['resources']))
            return null;

        $AsetData = [];

        foreach($Data['resources'] as $Resource){

            if(isset($Resource['format']) && $Resource['format'] == 'CSV')
                $AsetData = $Resource;


        }

        if(empty($AsetData['url']))
            return null;

        return $AsetData['url'];

    }

    public static function getLastAvailableDayData() : array {

        $CsvUrl = self::getCsvUrl();

        if(empty($CsvUrl))
            return[];

        $Content = file_get_contents($CsvUrl, false, stream_context_create(['http' => ['timeout' => self::getRequestTimeout()]]));
        $Content = implode("\n", array_slice(explode("\n", $Content), 0, self::getCsvRowsToAnalyze()));

        $File = fopen('php://temp', 'r+');

        fwrite($File, $Content);
        rewind($File);
        unset($Content);

        $Headers = fgetcsv($File, 0, ';');

        foreach($Headers as $Index => $Header){

            $Headers[$Index] = str_replace(' ', '', $Header);

        }

        $Data = [];

        while (($Row = fgetcsv($File, 0, ';')) !== false) {

            if (count($Headers) !== count($Row)) {
                continue;
            }

            $Data[] = array_combine($Headers, $Row);
        }

        fclose($File);

        $BaseDate = $Data[0]['DataBase'];

        foreach($Data as $Index => $Asset){

            if($Asset['DataBase'] != $BaseDate){
                unset($Data[$Index]);
            } else {

                $DataVencimento = DateTime::createFromFormat('d/m/Y', $Asset['DataVencimento']);

                $Data[$Index]['Ano'] = $DataVencimento->format('Y');
                $Data[$Index]['DataVencimento'] = $DataVencimento->format('Y-m-d');
                $Data[$Index]['DataBase'] = DateTime::createFromFormat('d/m/Y', $Asset['DataBase'])->format('Y-m-d');
                $Data[$Index]['TaxaCompraManha'] = (float) str_replace(',', '.', $Data[$Index]['TaxaCompraManha']);
                $Data[$Index]['TaxaVendaManha'] = (float) str_replace(',', '.', $Data[$Index]['TaxaVendaManha']);
                $Data[$Index]['PUCompraManha'] = (float) str_replace(',', '.', $Data[$Index]['PUCompraManha']);
                $Data[$Index]['PUVendaManha'] = (float) str_replace(',', '.', $Data[$Index]['PUVendaManha']);
                $Data[$Index]['PUBaseManha'] = (float) str_replace(',', '.', $Data[$Index]['PUBaseManha']);

            }

        }

        return $Data;

    }

}