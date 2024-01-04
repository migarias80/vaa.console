<?php

namespace utils;

use dto\RutaMHCDTO;
use \utils\LogUtils;

abstract class MHCParser
{

    static function LoadMHC($xmlFilePath) {
        try {
            if ($xmlFilePath != "" && file_exists($xmlFilePath)) {
                $xml = simplexml_load_file($xmlFilePath);
                return $xml;
            }
        } catch (Exception $exc) {
            LogUtils::INFO("Se produjo un error al cargar el archivo MHC", __CLASS__, __FUNCTION__, array($xmlFilePath));

        }
        return null;
    }

    static function GetRoutes($xmlFilePath) {
        $xml = MHCParser::LoadMHC($xmlFilePath);
        $rutas = [];
        try {
            if ($xml != null) {
                foreach ($xml->Hardware as $hardware) {
                    $sip_line_settings = $hardware->sip_line_settings;
                    $i = 0;
                    $eof = false;
                    do {
                        $sip_line_setting = $hardware->sip_line_settings->{'sip_line_setting_' . $i};
                        if ($sip_line_setting->name == null) {
                            $eof = true;
                        } else {
                            $rutas[] = new RutaMHCDTO([
                                'LINE_SETTING_ID'=>'sip_line_setting_' . $i, 
                                'RUTA'=>(string)$sip_line_setting->route]
                            );
                        }
                        $i++;
                    } while (!$eof);
                }
            }
            return $rutas;
        } catch (Exception $exc) {
            LogUtils::INFO("Se produjo un error al leer el archivo MHC", __CLASS__, __FUNCTION__, array($xmlFilePath));
            return [];
        }
    }

}