<?php

namespace dao;

use \model\Usuario;

interface IUsuarioDAO
{
    public function getUsuarioById($idUsuario);
    public function getUsuarioByNombre($nombreUsuario);
    public function getUsuariosByIdEmpresa($idEmpresa);
    public function nuevo($nombreUsuario, $idEmpresa, $fullName, $password, $id_profile, $lastEditUserId);
    public function modificar($id, $idEmpresa, $nombreUsuario, $fullName, $idProfile, $lastEditUserId);
    public function modificarMisDatos($id, $idEmpresa, $nombreUsuario, $fullName, $lastEditUserId);
    public function deshabilitar($idEmpresa, $id, $lastEditUserId);
    public function habilitar($idEmpresa, $id, $lastEditUserId);
    public function eliminar($idEmpresa, $id, $lastEditUserId);
    public function updateLastAccess($idUsuario, $idEmpresa, $ipAddress);
    public function getLastAccess($idUsuario, $idEmpresa);
    public function setPassword($idUsuario, $password, $id_empresa, $old_password, $lastEditUserId);
    public function getHistorial($idUsuario, $idEmpresa);
    public function getAllAccess($idUsuario, $idEmpresa);
}