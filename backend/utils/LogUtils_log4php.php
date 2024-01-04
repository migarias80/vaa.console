<?php

namespace utils;

use providers\Log4phpProvider;

/**
 * Class LogUtils, Clase estática que permite el manejo de logs dentro del sistema.
 * Se apoya en la clase Log4phpProvider para la integracion de log4php.
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
        $log4php = new Log4phpProvider(CONF_LOG_FILE, $filename, $method);
        $log4php->Info($message, $parameters);
    }

    /**
     * Static Method ERROR, Crea una entrada de tipo ERROR en el log
     * @param $message el mensaje a loguear
     * @param $filename el nombre o clase actual
     * @param $method el metodo actual
     * @param $parameters array de parametros
     */
    static function ERROR ($message, $filename, $method, $parameters=null) {
        $log4php = new Log4phpProvider(CONF_LOG_FILE, $filename, $method);
        $log4php->Error($message, $parameters);
    }
}