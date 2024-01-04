<?php

namespace dao;

use \model\VoiceMail;

interface IVoiceMailDAO
{
    public function getVoiceMail($idEmpresa, $id=null);
    public function guardar(VoiceMail $voiceMail);
    public function eliminar($idEmpresa, $id);
}