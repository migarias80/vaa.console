<?php

namespace service;

use \dto\FaxDTO;

interface IFaxService
{
    public function getFax($idEmpresa, $id=null);
    public function guardar(FaxDTO $faxDTO);
    public function eliminar($idEmpresa, $id);
}