<?php

namespace dao;

use \model\DepTransferOption;

interface IDepTransferOptionDAO
{
    public function getDepTransferOption($idDepartment);
    public function guardar(DepTransferOption $depTransferOption);
    public function eliminar($idDepartment);
}