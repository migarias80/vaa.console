<?php

/*
 * Readme:
 * http://logging.apache.org/log4php/
 */

namespace providers;

require_once 'vendor/apache/log4php/src/main/php/Logger.php';
require_once 'vendor/apache/log4php/src/main/php/LoggerLevel.php';

class Log4phpProvider
{

    private $log = null;
    private $method = null;

    function __construct($xmlConfig, $filename, $method) {
        \Logger::configure($xmlConfig);
        $this->log = \Logger::getLogger($filename);
        $this->log->method = $method;
    }

    function Info($message, $arrayParameters=null)
    {
        $this->LogMessage(\LoggerLevel::getLevelInfo(), $message, $arrayParameters);
    }

    function Warn($message, $arrayParameters=null)
    {
        $this->LogMessage(\LoggerLevel::getLevelWarn(), $message, $arrayParameters);
    }

    function Error($message, $arrayParameters=null)
    {
        $this->LogMessage(\LoggerLevel::getLevelError(), $message, $arrayParameters);
    }

    private function LogMessage($level, $message, $arrayParameters=null)
    {
        $parameters = null;
        if (is_array($arrayParameters)) {
            $parameters = implode(",", $arrayParameters);
        }
        switch ($level){
            case \LoggerLevel::getLevelInfo():
                $this->log->info("[" . $this->log->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
            case \LoggerLevel::getLevelWarn():
                $this->log->warn("[" . $this->log->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
            case \LoggerLevel::getLevelError():
                $this->log->error("[" . $this->log->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
        }
    }

}