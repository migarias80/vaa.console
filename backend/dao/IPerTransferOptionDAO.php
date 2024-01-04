<?php

namespace dao;

use \model\PerTransferOption;

interface IPerTransferOptionDAO
{
    public function getPerTransferOption($idPersona);
    public function guardar(PerTransferOption $perTransferOption);
    public function eliminar($idPersona);
    public function desvincularPersonaComoDestinoDeTransferencia($idPersona);
}