<?php

namespace dao;

use model\BandaHoraria;
use \model\Parametro;
use \model\Feriado;

interface IParametroDAO
{
    // OPCIONES VARIAS
    public function getConfirmationOptions();
    public function getTransferOption($taoCategoriesExclude=null);
    public function getOpcionesGramaticas();
    public function getDayTypes();
    public function getOperationModes();

    // BANDAS HORARIAS
    public function getBandasHorarias($idEmpresa);
    public function crearBandaHoraria(BandaHoraria $bandaHoraria);
    public function generarBandasHorariasParaUnaEmpresa($idEmpresa);
    public function eliminarBandaHoraria($idEmpresa, $ban_day_type, $opmCode, $start, $end);
    public function eliminarBandaHorariaPorDia($idEmpresa, $dayType);

    // PARAMETROS
    public function getParametros($idEmpresa, $parametrosFilter=null);
    public function getQueryValidation($parName, $idEmpresa);
    public function getQueryValidationByQueryId($queryId, $idEmpresa);
    public function modificarParametro(Parametro $parametro);
    public function generarParametrosParaUnaEmpresa($idEmpresa);

    // FERIADOS
    public function getFeriados($idEmpresa, $holDate=null);
    public function crearFeriado(Feriado $feriado);
    public function eliminarFeriado($idEmpresa, $holDate);

    // FAX Y VOICEMAIL
    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);
}