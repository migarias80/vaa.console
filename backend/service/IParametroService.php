<?php

namespace service;

use \dto\BandaHorariaDTO;
use \dto\ParametroDTO;
use \dto\FeriadoDTO;

interface IParametroService
{
    // OPCIONES VARIAS
    public function getConfirmationOptions();
    public function getTransferOptions($taoCategoriesExclude=null);
    public function getOpcionesGramaticas();
    public function getDayTypes();
    public function getOperationModes();

    // BANDAS HORARIAS
    public function getBandasHorarias($idEmpresa);
    public function crearBandaHoraria(BandaHorariaDTO $bandaHorariaDTO);
    public function modificarBandaHoraria(BandaHorariaDTO $bandaHorariaDTO);
    public function eliminarBandaHoraria($idEmpresa, $dayType, $opmCode, $start, $end);
    public function eliminarBandaHorariaPorDia($idEmpresa, $dayType);

    // PARAMETROS
    public function getParametros($idEmpresa, $parametrosFilter=null);
    public function getQueryValidation($parName, $idEmpresa);
    public function getQueryValidationByQueryId($queryId, $idEmpresa);
    public function modificarParametro(ParametroDTO $parametroDTO);

    // FERIADOS
    public function getFeriados($idEmpresa, $holDate=null);
    public function crearFeriado(FeriadoDTO $feriadoDTO);
    public function modificarFeriado(FeriadoDTO $feriadoDTO);
    public function eliminarFeriado($idEmpresa, $holDate);

    // FAX Y VOICEMAIL
    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);

    public function evalHabilitarEmpresa($idEmpresa);
}