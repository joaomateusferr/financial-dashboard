<?php

require dirname(__DIR__, 1).'/config.php';

use App\Services\MariaDB;

try{

    $KernelConnection = new MariaDB('kernel');

    $Queries = [
        "CREATE DATABASE kernel",
        "USE kernel",
        "CREATE TABLE users (
            ID SERIAL PRIMARY KEY,
            Name VARCHAR(255),
            Identifier VARCHAR(255),
            Email VARCHAR(255) NOT NULL,
            PasswordHash VARCHAR(255) NOT NULL,
            Type ENUM('STANDARD','ADMIN') NOT NULL DEFAULT 'STANDARD',
            CustomerServerID INT NOT NULL
        )",
        "CREATE INDEX IDX_Identifier ON users (Identifier)",
        "CREATE INDEX IDX_Email ON users (Email)",
        "CREATE TABLE sessions (
            ID SERIAL PRIMARY KEY,
            Token VARCHAR(96) NOT NULL UNIQUE,
            UserID BIGINT UNSIGNED NOT NULL,
            UserAgent VARCHAR(255) DEFAULT NULL,
            CreatedAt INT UNSIGNED NOT NULL DEFAULT (UNIX_TIMESTAMP()),
            ExpiresAt INT UNSIGNED NOT NULL,
            FOREIGN KEY (UserID) REFERENCES users(ID)
        )",
    ];

    foreach($Queries as $Sql){

        $Stmt = $KernelConnection->prepare($Sql);
        $Result = $Stmt->execute();

    }

} catch (Exception $Exception) {

    echo $Exception->getMessage()."\n";

} finally {

    $KernelConnection->close();

}