<?php

declare(strict_types=1);

use Slim\App;
use App\Middlewares\ErrorMiddleware;
use App\Middlewares\HttpNotFoundMiddleware;
use App\Middlewares\HttpMethodNotAllowedMiddleware;

return function (App $app) {

    $app->add(new HttpMethodNotAllowedMiddleware());
    $app->add(new HttpNotFoundMiddleware());
    $app->add(new ErrorMiddleware($app->getResponseFactory())); //It sits at the outermost layer to catch exceptions from routes and other middlewares.

};
