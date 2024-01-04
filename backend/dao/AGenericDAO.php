<?php

namespace dao;

class AGenericDAO
{

    public function BooleanToString($item, $considerEmpty=true) {
        if (!$considerEmpty) {
            if ($item === false) {
                return "F";
            }
            if ($item == "" || $item == null) {
                return null;
            }
        }
        if ($item == "") {
            return "F";
        } elseif ($item == "0") {
            return "F";
        } elseif ($item == "1") {
            return "T";
        } elseif ($item) {
            return "T";
        } elseif (!$item) {
            return "F";
        } else {
            return "F";
        }

    }

    public function StringToBoolean($item, $considerEmpty=true) {
        if (!$considerEmpty && ($item == "" || $item == null)) {
            return null;
        }
        if ($item == "") {
            return false;
        } elseif ($item == "F") {
            return false;
        } elseif ($item == "T") {
            return true;
        } else {
            return false;
        }
    }

    public function FlagEnconde($flagCurrentValue, $newValue, $newValuePos) {
        if ($newValue) {
            $flagCurrentValue |= 1<<$newValuePos;
        } else {
            $flagCurrentValue &= ~(1<<$newValuePos);
        }
        return $flagCurrentValue;
    }

    public function FlagDecode($flagValue, $flagPos) {
        /*if (!is_null($flagValue) && $flagValue == 0 && $flagPos == 0) {
            return true;
        }*/
        return (($flagValue & (1<<$flagPos)) != 0);
    }

}

/*
* Acceso por DTMF en Llamadas externas: ALLOW_HTDF_EXT  DEP_EXT_GUIDE_NUMBER
* Acceso por DTMF en Llamadas internas: ALLOW_HTDF_INT  DEP_INT_GUIDE_NUMBER
* Habilitar acceso por voz en Llamadas externas: ALLOW_VOZ_EXT
* Habilitar acceso por voz en Llamadas internas: ALLOW_VOZ_INT
* Reproducir mensaje informativo en transferencias supervisadas: PLAY_MSG_INFO_TRANF
* Utilizar mensaje de secretaria personal: MSG_SEC_PERSONAL
*
*/
abstract class FLAG_POS
{
    const ALLOW_HTDF_INT = 0;
    const ALLOW_HTDF_EXT = 1;
    const ALLOW_VOZ_INT = 2;
    const ALLOW_VOZ_EXT = 3;
    const MSG_SEC_PERSONAL = 4;
    const PLAY_MSG_INFO_TRANF = 5;
}

abstract class PHB_GRAMMAR
{
    const PNOMBRE_PAPELLIDO = 0; // Primer nombre y primer apellido
    const PNOMBRE_PAPELLIDO_SAPELLIDO = 1; // Primer nombre, primer apellido y segundo apellido
    const PNOMBRE_SNOMBRE_PAPELLIDO = 2; // Primer nombre, segundo nombre y primer apellido
    const PNOMBRE_SNOMBRE_PAPELLIDO_SAPELLIDO = 3; // Primer nombre, segundo nombre, primer apellido y segundo apellido
    const PNOMBRE_SAPELLIDO = 4; // Primer nombre y segundo apellido
    const SNOMBRE_PAPELLIDO = 5; // Segundo nombre y primer apellido
    const SNOMBRE_PAPELLIDO_SAPELLIDO = 6; // Segundo nombre, primer apellido y segundo apellido
    const SNOMBRE_SAPELLIDO = 7; // Segundo nombre y segundo apellido
    const PNOMBRE = 8; // Primer nombre
    const SNOMBRE = 9; // Segundo nombre
    const PNOMBRE_SNOMBRE = 10; // Primer nombre y segundo nombre
    const PAPELLIDO = 11; // Primer apellido
    const SAPELLIDO = 12; // Segundo apellido
    const PAPELLIDO_SAPELLIDO = 13; // Primer apellido y segundo apellido
}
