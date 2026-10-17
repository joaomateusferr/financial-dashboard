<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;
use App\Constants\SystemConstants;

try {

    $_SERVER['PROJECT_ROOT'] = dirname((new ReflectionClass(ClassLoader::class))->getFileName(), 3);
    $DotEnv = Dotenv::createImmutable($_SERVER['PROJECT_ROOT']);
    $DotEnv->load();
    $DotEnv->required(SystemConstants::getRequiredEnvironmentVariables())->notEmpty();

} catch (Exception $Exception) {

    if(php_sapi_name() === 'cli'){

        echo $Exception->getMessage()."\n";

    } else {

        http_response_code(501);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => true, 'result' => [$Exception->getMessage()]]);

    }


    exit;

}

