<?php

namespace dao;

use \model\Domain;

interface IDomainDAO
{
    public function getDomain($idEmpresa, $id=null, $regex=null);
    public function guardar(Domain $domain);
    public function eliminar($idEmpresa, $id);
}