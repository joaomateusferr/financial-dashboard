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

        $Hostname = gethostname();

        if(!empty($Hostname)){

            $Logger->pushProcessor(

                function (LogRecord $Record) use ($Hostname): LogRecord {

                    $Record->extra['hostname'] = $Hostname;
                    return $Record;

                }

            );

        }

        $Logger->$Level($Message, $Context);

        return true;

    }

    private static function formatBacktrace(array $Backtrace) : array {

        foreach($Backtrace as $Index => $Line){

            $Backtrace[$Index] = $Index.') '.$Line['class'].$Line['type'].$Line['function'].' -> '.$Line['file'].'('.$Line['line'].')';

        }

        return $Backtrace;

    }

    private static function getMostRecentLogFilePath() : string {

        $LogFolder = $_SERVER['PROJECT_ROOT'].$_SERVER['LOG_FILES_PATH'].'/';

        $Files = glob("$LogFolder*.log*");

        if(empty($Files))
            return '';

        return $Files[array_key_last($Files)];

    }

    public static function getMostRecentLogDate() : string {

        $MostRecentLogFilePath = self::getMostRecentLogFilePath();

        if(empty($MostRecentLogFilePath))
            return '';

        return pathinfo($MostRecentLogFilePath, PATHINFO_FILENAME);

    }

    public static function getLogLevels() : array {

        $LogLevels = [];

        foreach(Level::NAMES as $Index => $Name)
            $LogLevels[Level::VALUES[$Index]] = $Name;

        return $LogLevels;

    }

    public static function search(string $Date, string $Host, string $Identifier, int $Level, string $Message) : array {

        $LogFile = $_SERVER['PROJECT_ROOT'].$_SERVER['LOG_FILES_PATH'].'/'.$Date.'.log';

        if(!file_exists($LogFile))
            return [];

        $Logs = [];

        $Lines = file($LogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach($Lines as $Index => $Line){

            if(!json_validate($Line))
                continue;

            $Line = json_decode($Line, true);

            if (!empty($Host) && (empty($Line['extra']['hostname']) || $Line['extra']['hostname'] != $Host))
                continue;

            if(!empty($Identifier) && $Line['channel'] != $Identifier)
                continue;

            if(!empty($Level) && $Line['level'] != $Level)
                continue;

            $Channel = $Line['channel'];

            unset($Line['channel']);

            $Logs[$Channel][] = $Line;

            unset($Lines[$Index]);

        }

        unset($Lines);

        foreach($Logs as $Channel => $logs){

            $Logs[$Channel] = array_reverse($Logs[$Channel]);

        }

        return $Logs;

    }

}