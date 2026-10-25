<?php

namespace App\Middlewares;

use App\Services\Log;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Throwable;
use App\Services\ApiBase;

class ErrorMiddleware extends ApiBase implements MiddlewareInterface {

    public function process(Request $Request, Handler $Handler): Response {

        try {

            return $Handler->handle($Request);

        } catch (Throwable $Exception) {

            $Context = [];
            $Context['exception'] = $Exception;
            $Context['exception_class'] = $Exception::class;
            $Context['exception_file'] = $Exception->getFile();
            $Context['exception_line'] = $Exception->getLine();
            $Context['method'] = $Request->getMethod();
            $Context['path'] = $Request->getUri()->getPath();
            $Context['query'] = $Request->getUri()->getQuery();
            $Context['stack_trace'] = explode("\n", $Exception->getTraceAsString());

            Log::add('ERROR','api-exceptions', $Exception->getMessage(), $Context);

            return self::buildResponseFromFactory(['Internal Server Error'], 500, true);

        }
    }
}
