<?php

namespace serviceimpl;

use daoimpl\DepartmentSQLDAO;
use daoimpl\FuncionSQLDAO;
use daoimpl\PersonaSQLDAO;
use dto\BandaHorariaDTO;
use dto\ConfirmationOptionDTO;
use dto\DayTypeDTO;
use dto\FeriadoDTO;
use dto\GrammarOptionDTO;
use dto\OperationModeDTO;
use dto\TransferOptionDTO;
use model\ConfirmationOption;
use model\DayType;
use model\BandaHoraria;
use model\Feriado;
use model\OperationMode;
use model\Parametro;
use model\QueryValidation;
use service\IParametroService;
use daoimpl\ParametroSQLDAO;
use dto\ParametroDTO;
use dto\QueryValidationDTO;
use serviceimpl\EmpresaService;

class ParametroService implements IParametroService
{

    private $parametroDAO;
    private $funcionDAO;
    private $departamentoDAO;
    private $personasDAO;
    private $empresaService;

    function __construct() {
        $this->parametroDAO = new ParametroSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
        $this->departamentoDAO = new DepartmentSQLDAO();
        $this->personasDAO = new PersonaSQLDAO();
    }

    public function getConfirmationOptions()
    {
        $options = $this->parametroDAO->getConfirmationOptions();
        if ($options == null) {
            return null;
        }

        $optionsDTO = array();
        foreach ($options as $option) {
            $optionsDTO[] = new ConfirmationOptionDTO($option);
        }
        return $optionsDTO;
    }

    public function getTransferOptions($taoCategoriesExclude=null)
    {
        $options = $this->parametroDAO->getTransferOption($taoCategoriesExclude);
        if ($options == null) {
            return null;
        }

        $optionsDTO = array();
        foreach ($options as $option) {
            $optionsDTO[] = new TransferOptionDTO($option);
        }
        return $optionsDTO;
    }

    public function getOpcionesGramaticas()
    {
        $options = $this->parametroDAO->getOpcionesGramaticas();
        if ($options == null) {
            return null;
        }

        $optionsDTO = array();
        foreach ($options as $option) {
            $optionsDTO[] = new GrammarOptionDTO($option);
        }
        return $optionsDTO;
    }

    public function getDayTypes()
    {
        $dayTypes = $this->parametroDAO->getDayTypes();
        if ($dayTypes == null) {
            return null;
        }

        $dayTypesDTO = array();
        foreach ($dayTypes as $dayType) {
            $dayTypesDTO[] = new DayTypeDTO($dayType);
        }
        return $dayTypesDTO;
    }

    public function getOperationModes()
    {
        $operationModes = $this->parametroDAO->getOperationModes();
        if ($operationModes == null) {
            return null;
        }

        $operationModesDTO = array();
        foreach ($operationModes as $operationMode) {
            $operationModesDTO[] = new OperationModeDTO($operationMode);
        }
        return $operationModesDTO;
    }

    public function getBandasHorarias($idEmpresa)
    {
        $bandas = $this->parametroDAO->getBandasHorarias($idEmpresa);
        if ($bandas == null) {
            return null;
        }

        $bandasDTO = array();
        foreach ($bandas as $banda) {
            $bandasDTO[] = new BandaHorariaDTO($banda);
        }
        return $bandasDTO;
    }

    public function crearBandaHoraria(BandaHorariaDTO $bandaHorariaDTO) {
        $bandaHoraria = new BandaHoraria($bandaHorariaDTO);
        if ($bandaHoraria->getBanEndHour() == "2359") {
            $bandaHoraria->setBanEndHour("2400");
        }
        $this->parametroDAO->crearBandaHoraria($bandaHoraria);
    }

    public function eliminarBandaHorariaPorDia($idEmpresa, $dayType) {
        $this->parametroDAO->eliminarBandaHorariaPorDia($idEmpresa, $dayType);
    }

    /* public function crearBandaHoraria(BandaHorariaDTO $bandaHorariaDTO)
    {
        // Delete all day
        if ($bandaHorariaDTO->getBanDayTypeOld() != null) {
            $this->parametroDAO->eliminarBandaHorariaPorDia($bandaHorariaDTO->getBusinessId(), $bandaHorariaDTO->getBanDayTypeOld());
        } else {
            $this->parametroDAO->eliminarBandaHorariaPorDia($bandaHorariaDTO->getBusinessId(), $bandaHorariaDTO->getBanDayType());
        }

        // Crear banda original
        $bandaHoraria = new BandaHoraria($bandaHorariaDTO);
        $this->parametroDAO->crearBandaHoraria($bandaHoraria);

        // 1: Opuesto
        // 2: Vacio
        if ($bandaHorariaDTO->getBanComplement() == 1) {
            // Crear opuesto
            if ((intval($bandaHorariaDTO->getBanEndHour()) - intval($bandaHorariaDTO->getBanStartHour())) < 2400) {
                $bandaHoraria2 = new BandaHoraria($bandaHorariaDTO);
                if ($bandaHoraria2->getBanOpmCode() == "NOCTURNO") {
                    $bandaHoraria2->setBanOpmCode("DIURNO");
                } else {
                    $bandaHoraria2->setBanOpmCode("NOCTURNO");
                }

                $endBandaHoraria2 = $bandaHorariaDTO->getBanEndHour();
                if ($endBandaHoraria2 == "2400") {
                    $endBandaHoraria2 = "0000";
                }
                $bandaHoraria2->setBanStartHour($endBandaHoraria2);
                $bandaHoraria2->setBanEndHour($bandaHorariaDTO->getBanStartHour());

                // Si hay un rango en el medio, se deben crear 3 bandas horarias
                if (intval($bandaHorariaDTO->getBanEndHour()) < 2400 && intval($bandaHorariaDTO->getBanStartHour()) > 0) {
                    $bandaHoraria2->setBanEndHour("2400");
                    $this->parametroDAO->crearBandaHoraria($bandaHoraria2);

                    $bandaHoraria3 = $bandaHoraria2;
                    $bandaHoraria3->setBanStartHour("0000");
                    $bandaHoraria3->setBanEndHour($bandaHorariaDTO->getBanStartHour());
                    $this->parametroDAO->crearBandaHoraria($bandaHoraria3);

                } else {
                    $this->parametroDAO->crearBandaHoraria($bandaHoraria2);
                }
            }
        }

        return true;
    } */

    public function modificarBandaHoraria(BandaHorariaDTO $bandaHorariaDTO)
    {
        return $this->crearBandaHoraria($bandaHorariaDTO);
    }

    public function eliminarBandaHoraria($idEmpresa, $dayType, $opmCode, $start, $end) {
        $this->parametroDAO->eliminarBandaHoraria($idEmpresa, $dayType, $opmCode, $start, $end);
        return true;
    }

    public function getParametros($idEmpresa, $parametrosFilter=null)
    {
        $parametros = $this->parametroDAO->getParametros($idEmpresa, $parametrosFilter);
        if ($parametros == null) {
            return null;
        }

        $parametrosDTO = array();
        foreach ($parametros as $parametro) {
            $parametrosDTO[] = new ParametroDTO($parametro);
        }
        return $parametrosDTO;
    }

    public function getQueryValidation($parName, $idEmpresa)
    {
        $queryValidations = $this->parametroDAO->getQueryValidation($parName, $idEmpresa);
        if ($queryValidations == null) {
            return null;
        }

        $queryValidationsDTO = array();
        foreach ($queryValidations as $queryValidation) {
            $queryValidationsDTO[] = new QueryValidationDTO($queryValidation);
        }
        return $queryValidationsDTO;
    }

    public function getQueryValidationByQueryId($queryId, $idEmpresa)
    {
        $queryValidations = $this->parametroDAO->getQueryValidationByQueryId($queryId, $idEmpresa);
        if ($queryValidations == null) {
            return null;
        }

        $queryValidationsDTO = array();
        foreach ($queryValidations as $queryValidation) {
            $queryValidationsDTO[] = new QueryValidationDTO($queryValidation);
        }
        return $queryValidationsDTO;
    }

    public function modificarParametro(ParametroDTO $parametroDTO)
    {
        $parametro = new Parametro($parametroDTO);
        $this->parametroDAO->modificarParametro($parametro);

        /* if ($parametroDTO->getParName() == "CANT_MAX_PHONE_BOOK" || 
            $parametroDTO->getParName() == "CANT_MAX_DEPARTMENTS" ||
            $parametroDTO->getParName() == "CANT_MAX_LINES") {
            $this->evalHabilitarEmpresa($parametroDTO->getBusinessId());
        } */

        return true;
    }

    public function getFeriados($idEmpresa, $holDate = null)
    {
        $feriados = $this->parametroDAO->getFeriados($idEmpresa, $holDate);
        if ($feriados == null) {
            return null;
        }

        $feriadosDTO = array();
        foreach ($feriados as $feriado) {
            $feriadosDTO[] = new FeriadoDTO($feriado);
        }
        return $feriadosDTO;
    }

    public function crearFeriado(FeriadoDTO $feriadoDTO)
    {
        $auxFecha = date("Ymd", strtotime($feriadoDTO->getHolDate()));
        $feriadoDTO->setHolDate($auxFecha);

        $feriado = new Feriado($feriadoDTO);

        // TODO: VALIDAR DUPLICIDAD

        $this->parametroDAO->crearFeriado($feriado);

        return true;
    }

    public function modificarFeriado(FeriadoDTO $feriadoDTO)
    {
        $auxFecha = date("Ymd", strtotime($feriadoDTO->getHolDate()));
        $auxFechaOld = date("Ymd", strtotime($feriadoDTO->getHolDateOld()));
        $feriadoDTO->setHolDate($auxFecha);
        $feriadoDTO->setHolDateOld($auxFechaOld);

        if ($this->eliminarFeriado($feriadoDTO->getBusinessId(), $feriadoDTO->getHolDateOld())) {
            return $this->crearFeriado($feriadoDTO);
        } else {
            return false;
        }
    }

    public function eliminarFeriado($idEmpresa, $holDate)
    {
        $auxFecha = date("Ymd", strtotime($holDate));

        $this->parametroDAO->eliminarFeriado($idEmpresa, $auxFecha);

        return true;
    }

    public function evalHabilitarEmpresa($idEmpresa)
    {
        $empresaService = new EmpresaService();
        $empresa = $empresaService->getEmpresa($idEmpresa);
        $isOK = true;
        if ($empresa->getDnisRegex() == null || $empresa->getDnisRegex() == "") {
            $isOK = false;
        }
        if ($empresa->getDnisRegexExt() == null || $empresa->getDnisRegexExt() == "") {
            $isOK = false;
        }
        if ($empresa->getOutputRoute() == null || $empresa->getOutputRoute() == "") {
            $isOK = false;
        }
        
        $parametroDTO = new ParametroDTO();
        $parametroDTO->setBusinessId($idEmpresa);
        $parametroDTO->setParName("VAA_ENABLED");
        if (!$isOK) {
            $parametroDTO->setParValue("NO");
            $this->modificarParametro($parametroDTO);
            return;
        }
        
        $parametros = $this->parametroDAO->getParametros($idEmpresa, ["CANT_MAX_PHONE_BOOK", "CANT_MAX_DEPARTMENTS", "CANT_MAX_LINES"]);
        foreach ($parametros as $parametro) {
            if ($parametro->getParValue() == null || $parametro->getParValue() == "") {
                $isOK = false;
            }
        }

        if (!$isOK) {
            $parametroDTO->setParValue("NO");
        } else {
            $parametroDTO->setParValue("SI");
        }
        $this->modificarParametro($parametroDTO);
    }

    public function clearFaxDay($idEmpresa, $idFax) {
        $this->parametroDAO->clearFaxDay($idEmpresa, $idFax);
    }

    public function clearFaxNight($idEmpresa, $idFax) {
        $this->parametroDAO->clearFaxNight($idEmpresa, $idFax);
    }

    public function clearVoiceMailDay($idEmpresa, $idVoiceMail) {
        $this->parametroDAO->clearVoiceMailDay($idEmpresa, $idVoiceMail);
    }

    public function clearVoiceMailNight($idEmpresa, $idVoiceMail) {
        $this->parametroDAO->clearVoiceMailNight($idEmpresa, $idVoiceMail);
    }

}