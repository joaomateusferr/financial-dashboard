<?php

require __DIR__ . '/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

$Mode = isset($argv[1]) ? $argv[1] : 'CI';

$SupportedModes = ['CI', 'DEV'];
$DatabaseNetwork = 'network-db';
$DatabaseNetworkAlias = 'db';

if(!in_array($Mode, $SupportedModes))
    exit(1);    //Mode not supported

$ProjectRoot = dirname((new ReflectionClass(ClassLoader::class))->getFileName(), 3);
$EnvPath = $ProjectRoot.'/.env';

if(file_exists($EnvPath))
    unlink($EnvPath);

$Output = [];
$ResultCode = 0;

exec("openssl rand -base64 45 | tr -dc 'A-Za-z0-9-().!@?#,/;+' | head -c30", $Output, $ResultCode); //Creating a cryptographically strong random password

if(!empty($ResultCode) || !isset($Output[0]))
    exit(2);    //Unable to generate database password

$DatabasePassword = trim($Output[0]);
$DatabaseUser = 'root';

$Output = [];
$ResultCode = 0;

exec("hostname -I | awk '{print $2}'", $Output, $ResultCode);

if(!empty($ResultCode) || !isset($Output[0]))
    exit(3);    //Unable to get database host

$DatabaseHost = empty($Output[0]) ? $DatabaseNetworkAlias : trim($Output[0]);
$DatabasePort = 3306;

$Output = [];
$ResultCode = 0;

exec("docker run --name mailcatcher -d -p 1080:1080 -p 1025:1025 sj26/mailcatcher", $Output, $ResultCode);

if(!empty($ResultCode))
    exit(4);    //Unable to run mailcatcher container

$Output = [];
$ResultCode = 0;

exec("docker network create $DatabaseNetwork", $Output, $ResultCode);

if(!empty($ResultCode))
    exit(5);    //Unable to create database network

$Output = [];
$ResultCode = 0;

exec("docker run --name mariadb -d --network $DatabaseNetwork --network-alias $DatabaseNetworkAlias -e MYSQL_ROOT_PASSWORD=$DatabasePassword -e MYSQL_DATABASE=system -p 3306:3306 mariadb:latest", $Output, $ResultCode);

if(!empty($ResultCode))
    exit(6);    //Unable to run mariadb container

if($Mode != 'CI'){

    $Output = [];
    $ResultCode = 0;

    exec("docker run --name phpmyadmin -d --network $DatabaseNetwork -p 8080:80 -e PMA_HOST=$DatabaseNetworkAlias -e PMA_PORT=3306 phpmyadmin:latest", $Output, $ResultCode);

    if(!empty($ResultCode))
        exit(7);    //Unable to run phpmyadmin container

}

echo "Database:";

while (true) {

    echo '.';
    $Result = testDatabase(['Host' => $DatabaseHost, 'Port' => $DatabasePort, 'User' => $DatabaseUser, 'Password' => $DatabasePassword]);

    sleep(1);

    if(!is_null($Result))
        break;

}

echo "\n";


$Output = [];
$ResultCode = 0;

exec("openssl rand -base64 45 | tr -dc 'A-Za-z0-9-().!@?#,/;+' | head -c32", $Output, $ResultCode); //Creating a cryptographically strong random key

if(!empty($ResultCode) || !isset($Output[0]))
    exit(8);    //Unable to generate random key

$JwtKey = trim($Output[0]);

$EnvVariables = [
    'KERNEL_DB_HOST' => $DatabaseHost,
    'KERNEL_DB_PORT' => $DatabasePort,
    'COMMON_INFORMATION_DB_HOST' => $DatabaseHost,
    'COMMON_INFORMATION_DB_PORT' => $DatabasePort,
    'CUSTOMERS_SERVER_1_DB_HOST' => $DatabaseHost,
    'CUSTOMERS_SERVER_1_DB_PORT' => $DatabasePort,
    'DB_USER' => $DatabaseUser,
    'DB_PASSWORD' => $DatabasePassword,
    'JWT_KEY' => $JwtKey,
    'API_BASE_URL' => 'http://localhost:8888', //default value for the development environment
    'UI_API_REQUEST_TIMEOUT' => 5,
    'LOG_FILES_PATH' => '/logs',
];

$EnvContent = '';

foreach($EnvVariables as $Key => $Value){
    $EnvContent .="$Key=$Value\n";
}

$EnvContent = substr($EnvContent, 0, -1);
$EnvResult = file_put_contents($EnvPath, $EnvContent);

if(empty($EnvResult))
    exit(9); //Unable to load credentials

function testDatabase(array $Options) : ?int {

    $PDOOptions = [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_TIMEOUT => 1,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Make the default fetch be an associative array
    ];

    $Result = null;

    try {

        $DSN = 'mysql:host='.$Options['Host'].';port='.$Options['Port'].';charset=utf8';
        $PDO = new PDO($DSN, $Options['User'], $Options['Password'], $PDOOptions);
        $Sql = "SELECT 1 AS Result";
        $Stmt = $PDO->prepare($Sql);
        $Result = $Stmt->execute();

        if($Result && $Stmt->rowCount() > 0){
            $Result = $Stmt->fetch()['Result'];
        }

    } catch (Exception $Exception) {

        //this will generate an error anyway

    } finally {

        if(!empty($PDO)){

            try{
                $PDO->query('KILL CONNECTION_ID()');
            } catch (Exception $Exception){
                //this will generate an error anyway we only handle the error when killing the connection
            }

            $PDO = null;

        }

    }

    return $Result;

}
