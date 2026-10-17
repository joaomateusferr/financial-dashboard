<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Services\NFCE;

try{

    $URL = 'https://www.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx?p=35261050948371001573651630000697361007076947%7C2%7C1%7C1%7C98DDC0776766039EAB166DDC2C44D3E6D73E3928';
    $NFCE = new NFCE($URL);

    echo $NFCE->getName()."\n";
    echo $NFCE->getCNPJ()."\n";
    echo $NFCE->getAddress()."\n";
    echo $NFCE->getTaxes()."\n";
    print_r($NFCE->getItems());
    echo $NFCE->getIssueDate()."\n";
    echo $NFCE->getIssueDateTimestamp()."\n";

} catch (Exception $Exception) {

    error_log($Exception->getMessage());

}