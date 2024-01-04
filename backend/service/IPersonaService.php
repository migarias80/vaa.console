<?php

namespace service;

use \dto\DepTransferOptionDTO;
use dto\PersonaDTO;

interface IPersonaService
{
    public function getPersona($idEmpresa, $id=null, $id_dep=null, $incluirAcciones=false, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null);
    public function getPersonaMinNoCast($idEmpresa);
    public function guardarPersona(PersonaDTO $personaDTO);
    public function eliminarPersona($idEmpresa, $id);
    public function getSecretaria($idEmpresa);
    public function getTransferibles($idEmpresa);
    public function eliminarDepTransferOption($idPersona);
    public function getCountPersonas($idEmpresa);
    public function clearFaxDay($idEmpresa, $idFax);
    public function clearFaxNight($idEmpresa, $idFax);
    public function clearVoiceMailDay($idEmpresa, $idVoiceMail);
    public function clearVoiceMailNight($idEmpresa, $idVoiceMail);
    public function desvincularPersonaComoDestinoDeTransferencia($idPersona);
}