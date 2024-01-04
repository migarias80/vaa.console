<?php

namespace service;

use \dto\DepTransferOptionDTO;
use dto\DepartmentDTO;

interface IDepartmentService
{
    public function getDepartment($idEmpresa, $id=null, $name=null, $incluirAcciones=false, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null);
    public function guardarDepartment(DepartmentDTO $departmentDTO);
    public function eliminarDepartment($idEmpresa, $id);

    public function getDepTransferOption($idDepartment);
    public function guardarDepTransferOption(DepTransferOptionDTO $depTransferOption);
    public function eliminarDepTransferOption($idDepartment);

    public function getCountDepartments($idEmpresa);

    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);
}