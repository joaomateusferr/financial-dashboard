<?php

require dirname(__DIR__, 1).'/config.php';

use App\Services\MariaDB;

try{

    $KernelConnection = new MariaDB('kernel');

    $Queries = [
        "DROP DATABASE IF EXISTS kernel"
    ];

    foreach($Queries as $Sql){

        $Stmt = $KernelConnection->prepare($Sql);
        $Result = $Stmt->execute();

    }

    $CommonInformationConnection = new MariaDB('common-information');

    $Queries = [
        "DROP DATABASE IF EXISTS common_information"
    ];

    foreach($Queries as $Sql){

        $Stmt = $CommonInformationConnection->prepare($Sql);
        $Result = $Stmt->execute();

    }


} catch (Exception $Exception) {

    echo $Exception->getMessage()."\n";

} finally {

    $KernelConnection->close();
    $CommonInformationConnection->close();

}