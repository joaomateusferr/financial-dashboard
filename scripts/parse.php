<?php

require __DIR__ . '/../vendor/autoload.php';


try{

    $URL = 'https://www.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx?p=35261050948371001573651630000697361007076947%7C2%7C1%7C1%7C98DDC0776766039EAB166DDC2C44D3E6D73E3928';
    $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36';
    
    $Options = [ 'http' => ['ignore_errors' => true, 'timeout' => 10, 'user_agent' => $UserAgent,'method'  => 'GET']];
    $Page = @file_get_contents($URL, false, stream_context_create($Options));

    $Document = new DOMDocument();
    $Page = mb_convert_encoding($Page, 'HTML-ENTITIES', 'UTF-8');
    @$Document->loadHTML($Page);
    $XPath = new DOMXPath($Document);

    $Content = $XPath->query("//div[contains(@id, 'conteudo')]");

    if(empty($Content->length))
        exit;

    $Content = $Content->item(0);

    $Topo = $XPath->query(".//div[contains(@class, 'txtTopo')]", $Content);

    if(empty($Topo->length))
        exit;

    $Topo = $Topo->item(0);

    $Name = trim($Topo->nodeValue);

    $Text = $XPath->query(".//div[contains(@class, 'text')]", $Content);

    if($Text->length != 2)
        exit;

    $CNPJ = $Text->item(0)->nodeValue;
    $CNPJ = str_replace('CNPJ:','',$CNPJ);
    $CNPJ = trim($CNPJ);

    $Address = trim($Text->item(1)->nodeValue);
    $Tokens = explode(',', $Address);

    foreach($Tokens as $Index => $Token){

        $Tokens[$Index] = trim($Token);

        if(empty($Tokens[$Index]))
            unset($Tokens[$Index]);

    }

    $Address = implode(',', $Tokens);

    $Table = $XPath->query("//table[contains(@class, 'filterable')]");

    if(empty($Table->length))
        exit;

    $Table = $Table->item(0);

    $TableRows = $XPath->query(".//tr", $Table);

    foreach($TableRows as $Index => $Row){

        $TableDataLines = $XPath->query(".//td", $Row);

        if(empty($TableDataLines->length))
            continue;

        $TableDataLines = $TableDataLines->item(0);

        $Name = $XPath->query(".//span[contains(@class, 'txtTit')]", $TableDataLines);

        if(empty($Name->length))
            continue;

        $Name = trim($Name->item(0)->nodeValue);

        $Cod = $XPath->query(".//span[contains(@class, 'RCod')]", $TableDataLines);

        if(empty($Cod->length))
            continue;

        $Cod = trim($Cod->item(0)->nodeValue);
        $Cod = str_replace('(Código:','',$Cod);
        $Cod = str_replace(')','',$Cod);
        $Cod = trim($Cod);

        $Qtd = $XPath->query(".//span[contains(@class, 'Rqtd')]", $TableDataLines);

        if(empty($Qtd->length))
            continue;

        $Qtd = trim($Qtd->item(0)->nodeValue);
        $Qtd = str_replace('Qtde.:','',$Qtd);
        $Qtd = str_replace(',','.',$Qtd);
        $Qtd = (float) $Qtd;

        $Unit = $XPath->query(".//span[contains(@class, 'RUN')]", $TableDataLines);

        if(empty($Unit->length))
            continue;

        $Unit = trim($Unit->item(0)->nodeValue);
        $Unit = str_replace('UN:','',$Unit);
        $Unit = trim($Unit);

        $UnitPrice = $XPath->query(".//span[contains(@class, 'RvlUnit')]", $TableDataLines);

        if(empty($UnitPrice->length))
            continue;

        $UnitPrice = trim($UnitPrice->item(0)->nodeValue);
        $UnitPrice = str_replace('Vl. Unit.:','',$UnitPrice);
        $UnitPrice = trim($UnitPrice);
        $UnitPrice = preg_replace('/[\s\x{00A0}]+/u', '', $UnitPrice);
        $UnitPrice = str_replace(',','.',$UnitPrice);
        $UnitPrice = (float) $UnitPrice;

        var_dump($UnitPrice);exit;


        //echo $Document->saveHTML($TableDataLines->item(0));exit;


    }



    var_dump($Address);exit;


} catch (Exception $Exception) {

    error_log($Exception->getMessage());

}