<?php

namespace dao;

use \model\Persona;

interface IPersonaDAO
{
    public function getPersona($idEmpresa, $id=null, $id_dep=null, $is_sec=null, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null, $is_transf=null);
    public function getPersonaMinNoCast($idEmpresa);
    public function guardar(Persona $department);
    public function eliminar($idEmpresa, $id);
    public function desvincularSecretaria($id_secretaria);
    public function getCountPersonas($idEmpresa);
    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);
}