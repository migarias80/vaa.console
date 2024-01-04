<?php

namespace service;

use \dto\EmpresaDTO;

interface IEmpresaService
{
    public function getEmpresaPorURL($urlName);
    public function getEmpresa($id=null);
    public function guardar(EmpresaDTO $empresa);
    public function eliminar($id);
    public function setImage(EmpresaDTO $empresa);
    public function getEvaluacion($idEmpresa);
    public function setRegEx(EmpresaDTO $empresa);
    public function setOutputRoute(EmpresaDTO $empresa);
    public function getEvaluacionDNIS($nroEval);
    public function setConfiguracionGeneral(EmpresaDTO $empresaDTO);
    public function setTTSMode(EmpresaDTO $empresaDTO);
    public function getRutasMHCDeEmpresa($id);

    // TODO: Obsoleto
    public function setCantMaxPersonas(EmpresaDTO $empresa);
    public function setCantMaxDepartamentos(EmpresaDTO $empresa);
    public function setCantMaxLineas(EmpresaDTO $empresa);
    public function setContacto(EmpresaDTO $empresa);
    public function setNotas(EmpresaDTO $empresa);
    public function setEnabled(EmpresaDTO $empresa);
    public function setDisabled(EmpresaDTO $empresa);
}