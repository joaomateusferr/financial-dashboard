<?php

namespace App\Services;

use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;

class Log {

    public static function add(string $Level, string $Channel, string $Message, array $Context = [], bool $IncludeBacktrace = true): bool {

        $Logger = new Logger($Channel);
        $Today = date('Y-m-d');

        $FireHandler = new StreamHandler($_SERVER['PROJECT_ROOT'].$_SERVER['LOG_FILES_PATH'].'/'.$Today.'.log', Level::Debug);
        $FireHandler->setFormatter(new JsonFormatter());
        $Logger->pushHandler($FireHandler);

        /*
        $StdoutHandler = new StreamHandler('php://stdout', Level::Debug);
        $StdoutHandler->setFormatter(new JsonFormatter());
        $Logger->pushHandler($StdoutHandler);
        */

        $Level = strtolower($Level);
        $ValidName = false;

        foreach(Level::NAMES as $Name){

            $Name = strtolower($Name);

            if($Name == $Level)
                $ValidName = true;

        }

        if(empty($ValidName))
            return false;

        $Backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if(!empty($IncludeBacktrace) && !empty($Backtrace)){

            $Backtrace = self::formatBacktrace($Backtrace);

            $Logger->pushProcessor(

                function (LogRecord $Record) use ($Backtrace): LogRecord {

                    $Record->extra['backtrace'] = $Backtrace;
                    return $Record;

                }

            );

        }

        $Logger->$Level($Message, $Context);

        return true;

    }

    private static function formatBacktrace(array $Backtrace) : array {

        $Backtrace = array_reverse($Backtrace);

        foreach($Backtrace as $Index => $Line){

            $Position = $Index + 1;
            $Backtrace[$Index] = $Position.') '.$Line['class'].$Line['type'].$Line['function'].' -> '.$Line['file'].'('.$Line['line'].')';

        }

        return $Backtrace;

    }

}