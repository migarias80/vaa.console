<?php

namespace dao;

use \model\Department;

interface IDepartmentDAO
{
    public function getDepartment($idEmpresa, $id=null, $name=null, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null);
    public function guardar(Department $department);
    public function eliminar($idEmpresa, $id);
    public function eliminarPersonasDelDepartamento($idEmpresa, $id);
    public function getCountDepartments($idEmpresa);
    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);
}