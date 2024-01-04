<?php

namespace dao;

interface IEstadisticaDAO
{
    public function getLlamadas($idEmpresa, $fechaDesde, $fechaHasta);
    public function getTroncales($idEmpresa, $fechaDesde, $fechaHasta);
    public function getTroncalesMaximos($idEmpresa, $fechaDesde, $fechaHasta);
    public function getDetalleTroncal($idEmpresa, $fecha);
}
