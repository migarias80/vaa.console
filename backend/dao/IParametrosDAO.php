<?php

namespace dao;

interface IParametrosDAO
{
    public function getFax($idEmpresa, $id=null);
    public function guardar(Fax $fax);
    public function eliminar($idEmpresa, $id);
}