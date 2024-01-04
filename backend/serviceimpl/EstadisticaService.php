<?php

namespace serviceimpl;

use daoimpl\FuncionSQLDAO;
use dto\EstadisticaDTO;
use model\Estadistica;
use service\IEstadisticaService;
use daoimpl\EstadisticaSQLDAO;

class EstadisticaService implements IEstadisticaService
{

    private $estadisticaDAO;

    function __construct() {
        $this->estadisticaDAO = new EstadisticaSQLDAO();
    }

    function getLlamadas($idEmpresa, $fechaDesde, $fechaHasta) {
        $estadisticas = $this->estadisticaDAO->getLlamadas($idEmpresa, $fechaDesde, $fechaHasta);
        if ($estadisticas == null) {
            return null;
        }

        $estadisticasDTO = array();
        foreach ($estadisticas as $estadistica) {
            $estadisticasDTO[] = new EstadisticaDTO($estadistica);
        }
        return $estadisticasDTO;
    }

    function getTroncales($idEmpresa, $fechaDesde, $fechaHasta) {
        $estadisticas = $this->estadisticaDAO->getTroncales($idEmpresa, $fechaDesde, $fechaHasta);
        if ($estadisticas == null) {
            return null;
        }

        $estadisticasDTO = array();
        foreach ($estadisticas as $estadistica) {
            $estadisticaDTO = new EstadisticaDTO($estadistica);
            $porcentaje = ($estadistica->getDar_value() * 100) / $estadistica->getValorTotalEnElDia();
            $estadisticaDTO->setPorcentajeEnElDia(round($porcentaje, 2));
            $estadisticasDTO[] = $estadisticaDTO;
        }
        return $estadisticasDTO;
    }

    function getTroncalesMaximos($idEmpresa, $fechaDesde, $fechaHasta) {
        $estadisticas = $this->estadisticaDAO->getTroncalesMaximos($idEmpresa, $fechaDesde, $fechaHasta);
        if ($estadisticas == null) {
            return null;
        }

        $estadisticasDTO = array();
        foreach ($estadisticas as $estadistica) {
            $estadisticasDTO[] = new EstadisticaDTO($estadistica);
        }
        return $estadisticasDTO;
    }

    function getDetalleTroncal($idEmpresa, $fecha) {
        $estadisticas = $this->estadisticaDAO->getDetalleTroncal($idEmpresa, $fecha);
        if ($estadisticas == null) {
            return null;
        }

        $estadisticasDTO = array();
        foreach ($estadisticas as $estadistica) {
            $estadisticaDTO = new EstadisticaDTO($estadistica);
            $porcentaje = ($estadistica->getDar_value() * 100) / $estadistica->getValorTotalEnElDia();
            $estadisticaDTO->setPorcentajeEnElDia(round($porcentaje, 2));
            $estadisticasDTO[] = $estadisticaDTO;
        }
        return $estadisticasDTO;
    }
    
}