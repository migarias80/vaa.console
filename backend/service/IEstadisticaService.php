<?php

namespace service;

use \dto\EstadisticaDTO;

interface IEstadisticaService
{
    public function getLlamadas($idEmpresa, $fechaDesde, $fechaHasta);
    public function getTroncales($idEmpresa, $fechaDesde, $fechaHasta);
    public function getTroncalesMaximos($idEmpresa, $fechaDesde, $fechaHasta);
    public function getDetalleTroncal($idEmpresa, $fecha);
}