<?php

namespace service;

use dto\UsuarioDTO;
use dto\LoginDTO;

interface IUsuarioService
{
    public function login(LoginDTO $loginDTO);
    public function getUsuarioById($idUsuario);
    public function getUsuarioByNombreYEmpresa($nombreUsuario, $idEmpresa);
    public function getUsuariosByIdEmpresa($idEmpresa);
    public function getFuncionesDelUsuario($idUsuario);
    public function nuevo(UsuarioDTO $usuarioDTO);
    public function modificar(UsuarioDTO $usuarioDTO);
    public function modificarMisDatos(UsuarioDTO $usuarioDTO);
    public function deshabilitar($idEmpresa, $id, $lastEditUserId);
    public function habilitar($idEmpresa, $id, $lastEditUserId);
    public function eliminar($idEmpresa, $id, $lastEditUserId);
    public function updateLastAccess($usuarioUltimoAcceso);
    public function getLastAccess($idUsuario, $idEmpresa);
    public function updateMyPassword(UsuarioDTO $usuarioDTO, $oldPassword);
    public function setPassword(UsuarioDTO $usuarioDTO);
    public function setSAPassword(UsuarioDTO $usuarioDTO);
    public function getHistorial($idUsuario, $idEmpresa);
    public function getAllAccess($idUsuario, $idEmpresa);
}