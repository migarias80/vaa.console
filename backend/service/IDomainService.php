<?php

namespace service;

use \dto\DomainDTO;

interface IDomainService
{
    public function getDomain($idEmpresa, $id=null, $regex=null);
    public function guardar(DomainDTO $faxDTO);
    public function eliminar($idEmpresa, $id);
}