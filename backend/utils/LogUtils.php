<?php

namespace utils;

use providers\MonologProvider;

/**
 * Class LogUtils, Clase estática que permite el manejo de logs dentro del sistema.
 * Se apoya en la clase MonologProvider para la integracion de Monolog.
 * @package utils
 */
abstract class LogUtils
{
    /**
     * Static Method INFO, Crea una entrada de tipo INFO en el log
     * @param $message el mensaje a loguear
     * @param $filename el nombre o clase actual
     * @param $method el metodo actual
     * @param $parameters array de parametros
     */
    static function INFO ($message, $filename, $method, $parameters=null) {
        $logger = new MonologProvider(CONF_LOG_FILE, $filename, $method);
        $logger->Info($message, $parameters);
    }

    /**
     * Static Method ERROR, Crea una entrada de tipo ERROR en el log
     * @param $message el mensaje a loguear
     * @param $filename el nombre o clase actual
     * @param $method el metodo actual
     * @param $parameters array de parametros
     */
    static function ERROR ($message, $filename, $method, $parameters=null) {
        $logger = new MonologProvider(CONF_LOG_FILE, $filename, $method);
        $logger->Error($message, $parameters);
    }
}