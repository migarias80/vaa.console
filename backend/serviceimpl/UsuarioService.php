<?php

namespace serviceimpl;

use daoimpl\FuncionSQLDAO;
use daoimpl\UsuarioSQLDAO;
use dto\LoginDTO;
use dto\UsuarioAUDTO;
use \dto\UsuarioDTO;
use \dto\FuncionDTO;
use \daoimpl\UsuarioMySQLDAO;
use dto\UsuarioUltimoAccesoDTO;
use exception\UsuarioRepetidoException;
use model\UsuarioPerfil;
use \utils\LogUtils;
use \utils\SecurityUtils;
use \service\IUsuarioService;
use \serviceimpl\EmpresaService;
use \utils\CommonUtils;
use \dto\ParametroDTO;
use \serviceimpl\ParametroService;

class UsuarioService implements IUsuarioService
{

    private $usuarioDAO;
    private $funcionDAO;
    private $empresaService;

    function __construct() {
        $this->usuarioDAO = new UsuarioSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
        $this->empresaService = new EmpresaService();
    }

    function login(LoginDTO $loginDTO) {
        $result = ["status"=>false, "message"=>null];

        // Verificacion de empresa
        $empresa = null;
        if ($loginDTO->getUrlEmpresa()) {
            $empresa = $this->empresaService->getEmpresaPorURL($loginDTO->getUrlEmpresa());
            if ($empresa == null) {
                $result["message"] = "Empresa No encontrada";
                $result["status"] = false;
                LogUtils::INFO("Empresa No encontrada", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                return $result;
            }

            if ($empresa->getVaaActive() == null) {
                $result["message"] = "Parametro VAA_ACTIVE No encontrado";
                $result["status"] = false;
                return $result;
            } else if ($empresa->getVaaActive() != 1) {
                $result["message"] = "Empresa No activada";
                $result["status"] = false;
                LogUtils::INFO("Empresa No activada", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                return $result;
            }
        } else {
            LogUtils::INFO("Usuario administrador", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
        }

        // Obtener los usuarios por nombre
        $usuarios = $this->usuarioDAO->getUsuarioByNombre($loginDTO->getNombreUsuario());
        if ($usuarios == null || count($usuarios) == 0) {
            $result["message"] = "Nombre de usuario incorrecto";
            $result["status"] = false;
            LogUtils::INFO("Usuario NO encontrado", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
            return $result;
        }

        // Recorriendo los usuarios y matcheando por password y empresa
        $password = SecurityUtils::generarPassword($loginDTO->getNombreUsuario(), $loginDTO->getPassword());
        foreach ($usuarios as $usuario) {
            if ($usuario->getPassword() == $password) {
                if ($empresa == null) {
                    if ($usuario->getIdEmpresa() == CommonUtils::EDS_ID()) {
                        if (!$usuario->getActive()) {
                            $result["message"] = "El usuario no se encuentra habilitado para operar la consola";
                            $result["status"] = false;
                            LogUtils::INFO("Usuario NO habilitado", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                            return $result;
                        } else {
                            LogUtils::INFO("Usuario encontrado", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                            $result["message"] = $usuario->getId();
                            $result["status"] = true;
                            return $result;
                        }
                    }
                } else {
                    if ($usuario->getIdEmpresa() == $empresa->getId()) {
                        if (!$usuario->getActive()) {
                            $result["message"] = "El usuario no se encuentra habilitado para operar la consola";
                            $result["status"] = false;
                            LogUtils::INFO("Usuario NO habilitado", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                            return $result;
                        } else {
                            LogUtils::INFO("Usuario encontrado", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
                            $result["message"] = $usuario->getId();
                            $result["status"] = true;
                            return $result;
                        }
                    }
                }
            }
        }

        $result["message"] = "Nombre de usuario o contraseña incorrecta";
        $result["status"] = false;
        LogUtils::INFO("Datos de acceso incorrectos", __CLASS__, __FUNCTION__, array($loginDTO->getNombreUsuario()));
        return $result;
    }

    function getUsuarioById($id) {
        $usuario = $this->usuarioDAO->getUsuarioById($id);
        if ($usuario == null) {
            return null;
        }

        $usuarioDTO = new UsuarioDTO($usuario);
        return $usuarioDTO;
    }

    function getUsuarioByNombreYEmpresa($nombreUsuario, $idEmpresa) {
        $usuarios = $this->usuarioDAO->getUsuarioByNombre($nombreUsuario);
        $usuarioDTO = null;
        foreach ($usuarios as $usuario) {
            if ($usuario->getIdEmpresa() == $idEmpresa) {
                $usuarioDTO = new UsuarioDTO($usuario);
            }
        }

        return $usuarioDTO;
    }

    function getUsuariosByIdEmpresa($idEmpresa) {
        $usuarios = $this->usuarioDAO->getUsuariosByIdEmpresa($idEmpresa);

        $usuariosDTO = array();
        foreach ($usuarios as $usuario) {
            $usuariosDTO[] = new UsuarioDTO($usuario);
        }
        return $usuariosDTO;
    }

    function getFuncionesDelUsuario($idUsuario) {
        $funciones = $this->funcionDAO->getFuncionesByIdUsuario($idUsuario);

        $funcionesDTO = array();
        foreach ($funciones as $funcion) {
            $funcionesDTO[] = new FuncionDTO($funcion);
        }
        return $funcionesDTO;
    }

    public function nuevo(UsuarioDTO $usuarioDTO)
    {
        $usuarioBuscado = $this->getUsuarioByNombreYEmpresa($usuarioDTO->getNombreUsuario(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getFullName());
        if ($usuarioBuscado != null) {
            throw new UsuarioRepetidoException($usuarioDTO->getNombreUsuario());
        } else {
            $password = SecurityUtils::generarPassword($usuarioDTO->getNombreUsuario(), $usuarioDTO->getPassword());
            return $this->usuarioDAO->nuevo($usuarioDTO->getNombreUsuario(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getFullName(), $password, $usuarioDTO->getIdProfile(), $usuarioDTO->getLastEditUserId());
        }
    }

    public function modificar(UsuarioDTO $usuarioDTO)
    {
        $usuarioBuscado = $this->getUsuarioByNombreYEmpresa($usuarioDTO->getNombreUsuario(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getFullName());
        if ($usuarioBuscado != null) {
            if ($usuarioBuscado->getId() != $usuarioDTO->getId()) {
                throw new UsuarioRepetidoException($usuarioDTO->getNombreUsuario());
                return false;
            }
        }
        return $this->usuarioDAO->modificar($usuarioDTO->getId(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getNombreUsuario(), $usuarioDTO->getFullName(), $usuarioDTO->getIdProfile(), $usuarioDTO->getLastEditUserId());
    }

    public function modificarMisDatos(UsuarioDTO $usuarioDTO)
    {
        $usuarioBuscado = $this->getUsuarioByNombreYEmpresa($usuarioDTO->getNombreUsuario(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getFullName());
        if ($usuarioBuscado != null) {
            if ($usuarioBuscado->getId() != $usuarioDTO->getId()) {
                throw new UsuarioRepetidoException($usuarioDTO->getNombreUsuario());
                return false;
            }
        }
        return $this->usuarioDAO->modificarMisDatos($usuarioDTO->getId(), $usuarioDTO->getIdEmpresa(), $usuarioDTO->getNombreUsuario(), $usuarioDTO->getFullName(), $usuarioDTO->getLastEditUserId());
    }

    public function deshabilitar($idEmpresa, $id, $lastEditUserId) {
        $this->usuarioDAO->deshabilitar($idEmpresa, $id, $lastEditUserId);
    }

    public function habilitar($idEmpresa, $id, $lastEditUserId) {
        $this->usuarioDAO->habilitar($idEmpresa, $id, $lastEditUserId);
    }

    public function eliminar($idEmpresa, $id, $lastEditUserId) {
        $this->usuarioDAO->eliminar($idEmpresa, $id, $lastEditUserId);
    }

    public function updateLastAccess($usuarioUltimoAcceso) {
        $this->usuarioDAO->updateLastAccess($usuarioUltimoAcceso->getUserId(), $usuarioUltimoAcceso->getBusinessId(), $usuarioUltimoAcceso->getIpAddress());
    }

    public function getLastAccess($idUsuario, $idEmpresa)
    {
        $usuarioUltimoAcceso = $this->usuarioDAO->getLastAccess($idUsuario, $idEmpresa);

        $usuarioUltimoAccesoDTO = null;
        if ($usuarioUltimoAcceso != null) {
            $usuarioUltimoAccesoDTO = new UsuarioUltimoAccesoDTO($usuarioUltimoAcceso);
        }

        return $usuarioUltimoAccesoDTO;
    }

    public function updateMyPassword(UsuarioDTO $usuarioDTO, $oldPassword)
    {
        $usuario = $this->getUsuarioById($usuarioDTO->getId());
        if ($usuario == null) {
            LogUtils::INFO("Usuario NO encontrado", __CLASS__, __FUNCTION__, array($usuarioDTO->getId()));
            throw new \Exception("El usuario no fue encontrado");
        }
        $oldPassword = SecurityUtils::generarPassword($usuario->getNombreUsuario(), $oldPassword);
        if ($usuario->getPassword() != $oldPassword) {
            LogUtils::INFO("Password anterior NO coincide", __CLASS__, __FUNCTION__, array($usuarioDTO->getId()));
            throw new \Exception("El password anterior no coincide");
        }

        $newPassword = SecurityUtils::generarPassword($usuario->getNombreUsuario(), $usuarioDTO->getPassword());
        return $this->usuarioDAO->setPassword(
            $usuarioDTO->getId(),
            $newPassword,
            $usuarioDTO->getIdEmpresa(),
            $oldPassword,
            $usuarioDTO->getLastEditUserId());
    }

    public function setPassword(UsuarioDTO $usuarioDTO)
    {
        $usuario = $this->getUsuarioById($usuarioDTO->getId());
        if ($usuario == null) {
            LogUtils::INFO("Usuario NO encontrado", __CLASS__, __FUNCTION__, array($usuarioDTO->getId()));
            throw new \Exception("El usuario no fue encontrado");
        }
        if ($usuario->getIdProfile() == UsuarioPerfil::SA) {
            LogUtils::INFO("El usuario a actualizar el password NO puede ser SA", __CLASS__, __FUNCTION__, array($usuarioDTO->getId()));
            throw new \Exception("El usuario a actualizar el password NO puede ser SA");
        }
        $password = SecurityUtils::generarPassword($usuario->getNombreUsuario(), $usuarioDTO->getPassword());
        return $this->usuarioDAO->setPassword(
            $usuarioDTO->getId(),
            $password,
            $usuario->getIdEmpresa(),
            $usuario->getPassword(),
            $usuarioDTO->getLastEditUserId());
    }

    public function setSAPassword(UsuarioDTO $usuarioDTO)
    {
        $usuario = $this->getUsuarioById($usuarioDTO->getId());
        if ($usuario == null) {
            LogUtils::INFO("Usuario NO encontrado", __CLASS__, __FUNCTION__, array($usuarioDTO->getId()));
            return false;
        }
        $password = SecurityUtils::generarPassword($usuario->getNombreUsuario(), $usuarioDTO->getPassword());
        return $this->usuarioDAO->setPassword(
            $usuarioDTO->getId(),
            $password,
            $usuario->getIdEmpresa(),
            $usuario->getPassword(),
            $usuarioDTO->getLastEditUserId());
    }

    public function getHistorial($idUsuario, $idEmpresa)
    {
        $usuariosAU = $this->usuarioDAO->getHistorial($idUsuario, $idEmpresa);

        $usuariosAUDTO = array();
        foreach ($usuariosAU as $usuarioAU) {
            $usuarioAUDTO = new UsuarioAUDTO($usuarioAU);
            $usuariosAUDTO[] = $usuarioAUDTO;
        }

        return $usuariosAUDTO;
    }

    public function getAllAccess($idUsuario, $idEmpresa)
    {
        $usuarioAccesos = $this->usuarioDAO->getAllAccess($idUsuario, $idEmpresa);

        $usuarioUltimosAccesosDTO = array();
        foreach ($usuarioAccesos as $usuarioAcceso) {
            $usuarioUltimoAccesoDTO = new UsuarioUltimoAccesoDTO($usuarioAcceso);
            $usuarioUltimosAccesosDTO[] = $usuarioUltimoAccesoDTO;
        }

        return $usuarioUltimosAccesosDTO;
    }

}