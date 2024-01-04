<?php

namespace service;

use \dto\VoiceMailDTO;

interface IVoiceMailService
{
    public function getVoiceMail($idEmpresa, $id=null);
    public function guardar(VoiceMailDTO $voiceMailDTO);
    public function eliminar($idEmpresa, $id);
}