<?php

namespace dao;

use \model\Empresa;

interface IEmpresaDAO
{
    public function getEmpresaPorURL($urlName);
    public function getEmpresa($id=null);
    public function guardar(Empresa $emprsa);
    public function eliminar($id);
    public function setImage(Empresa $emprsa);
    public function setRegEx(Empresa $emprsa);
    public function setRegExExt(Empresa $emprsa);
    public function setOutputRoute(Empresa $emprsa);

    public function setConfiguracionGeneral(Empresa $empresa);
    public function setTTSMode(Empresa $empresa);

    // TODO: Obsoleto
    public function setCantMaxPersonas(Empresa $empresa);
    public function setCantMaxDepartamentos(Empresa $empresa);
    public function setCantMaxLineas(Empresa $empresa);
    public function setContacto(Empresa $empresa);
    public function setNotas(Empresa $empresa);
    public function setEnabled(Empresa $empresa);
    public function setDisabled(Empresa $empresa);
}