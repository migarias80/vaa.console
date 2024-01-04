<?php

namespace providers;

use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Configuration\JsonConfiguration;
use Monolog\LogLevel;

class MonologProvider
{

    private $log = null;
    private $method = null;
	private $levelInfo = 200;
	private $levelWarn = 300;
	private $levelError = 400;

    function __construct($fileConfig, $filename, $method) {
		$config = json_decode(file_get_contents($fileConfig), true);
		
		$this->log = new Logger('vaa_logger');
		foreach ($config['handlers'] as $name => $handlerConfig) {
			$handler = $this->createHandler($handlerConfig);
			$this->log->pushHandler($handler);
		}
		$this->log->setHandlers($this->log->getHandlers(), $config['processors']);
		$this->log->setTimezone(new \DateTimeZone($config['timezone'] ?? date_default_timezone_get()));
		
		$this->method = $method;
    }
	
	function createHandler(array $config) {
		$type = $config['type'];
		$formatter = $this->createFormatter($config['formatter'] ?? []);
		unset($config['type'], $config['formatter']);

		switch ($type) {
			case 'stream':
				return new StreamHandler($config['path'], $config['level'] ?? 'DEBUG', true, $config['permission'] ?? null, $config['bubble'] ?? true);
			case 'rotating_file':
				return new RotatingFileHandler($config['path'], $config['max_files'], $config['level'] ?? 'DEBUG', $config['bubble'] ?? true, $config['file_permission'] ?? null, $config['use_locking'] ?? false, $formatter, $config['filename_format'] ?? null);
			default:
				throw new InvalidArgumentException(sprintf('The handler type "%s" is not supported.', $type));
		}
	}

	function createFormatter(array $config) {
		$class = $config['class'] ?? null;
		unset($config['class']);

		switch ($class) {
			case LineFormatter::class:
				return new LineFormatter($config['format'] ?? null, $config['datetime_format'] ?? null, $config['allow_inline_line_breaks'] ?? false, $config['ignore_empty_context_and_extra'] ?? false);
			default:
				return null;
		}
	}

    function Info($message, $arrayParameters=null)
    {
        $this->LogMessage($this->levelInfo, $message, $arrayParameters);
    }

    function Warn($message, $arrayParameters=null)
    {
        $this->LogMessage($this->levelWarn, $message, $arrayParameters);
    }

    function Error($message, $arrayParameters=null)
    {
        $this->LogMessage($this->levelError, $message, $arrayParameters);
    }

    private function LogMessage($level, $message, $arrayParameters=null)
    {
        $parameters = null;
        if (is_array($arrayParameters)) {
            $parameters = implode(",", $arrayParameters);
        }
        switch ($level){
            case $this->levelInfo:
                $this->log->info("[" . $this->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
            case $this->levelWarn:
                $this->log->warn("[" . $this->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
            case $this->levelError:
                $this->log->error("[" . $this->method . "] " . $message . (($parameters != null)? " - " . $parameters : ""));
                break;
        }
    }

}
