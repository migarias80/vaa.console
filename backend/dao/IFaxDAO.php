<?php

namespace dao;

use \model\Fax;

interface IFaxDAO
{
    public function getFax($idEmpresa, $id=null);
    public function guardar(Fax $fax);
    public function eliminar($idEmpresa, $id);
}