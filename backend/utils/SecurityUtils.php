<?php

namespace utils;

use exception\InvalidTokenException;
use exception\UsuarioNoEncontradoException;
use providers\JWTProvider;
use exception\UsuarioSinPermisosException;
use serviceimpl\UsuarioService;
use \Exception;


/**
 * Class SecurityUtils, Clase estática que permite el manejo de seguridad del usuario en el sistema.
 * Se apoya en la clase JWTProvider para la generación del Token.
 * @package utils
 */
abstract class SecurityUtils
{

    /**
     * Static Method GetToken, Genera un Token para el usuario que recibe como parametro
     * @param $idUsuario
     * @return string
     * @throws UsuarioNoEncontradoException
     */
    static function GetToken($idUsuario) {
        // Get datos del usuario
        $srvUsuario = new UsuarioService();
        $usuarioDTO = $srvUsuario->getUsuarioById($idUsuario);
        if ($usuarioDTO == null) {
            throw new UsuarioNoEncontradoException();
        }

        // Get funciones del usuario
        $listaFuncionesDelUsuario = [];
        $usuarioSrv = new UsuarioService();
        $funcionesDelUsuario = $usuarioSrv->getFuncionesDelUsuario($idUsuario);
        foreach ($funcionesDelUsuario as $funcionDelUsuario) {
            $itemFuncionDelUsuario = array("funcion"=>$funcionDelUsuario->getDesc());
            $listaFuncionesDelUsuario[] = $itemFuncionDelUsuario;
        }

        // Get perfil del usuario
        $perfil = $usuarioDTO->getIdProfile();

        // Agregado de datos adicionales al token
        $array = array(
            "uname" => $usuarioDTO->getNombreUsuario(),
            "ufullname" => $usuarioDTO->getFullName(),
            "bid" => $usuarioDTO->getIdEmpresa(),
            "funciones" => $listaFuncionesDelUsuario,
            "pid" => $perfil
        );

        // Armado del token y retorno de datos
        $jwtProvider = new JWTProvider();
        return $jwtProvider->Get($idUsuario, $array);
    }


    /**
     * Static Method CheckPermissions, Verifica si el usuario posee permisos para realizar la acción solicitada
     * Si $idEmpresaVerificar es completado entonces verifica que el permiso corresponda a dicha empresa
     * @param $token
     * @param $action
     * @param null $idEmpresaVerificar
     * @return object
     * @throws InvalidTokenException
     * @throws UsuarioSinPermisosException
     */
    static function CheckPermissions($token, $action, $idEmpresaVerificar=null) {
        // Validacion del token
        if (is_array($token)) {
            if (empty($token)) {
                throw new InvalidTokenException("Token no definido");
            }
            $token = $token[0];
        }

        // Decode del token
        try {
            $jwtProvider = new JWTProvider();
            $decoded = $jwtProvider->Decode($token);
        } catch (InvalidTokenException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (Exception $e) {
            throw new InvalidTokenException($e->getMessage());
        }
        if ($decoded == null) {
            throw new InvalidTokenException();
        }

        // Validacion de permisos segun accion y empresa
        $permitido = false;
        if ($decoded->bid == 1) {
            $permitido = true;
        } else {
            foreach ($decoded->funciones as $funcionAsociada) {
                if ($funcionAsociada->funcion == $action) {
                    if ($idEmpresaVerificar == null) {
                        $permitido = true;
                        break;
                    }
                    if ($decoded->bid == $idEmpresaVerificar) {
                        $permitido = true;
                        break;
                    }
                }
            }
        }

        if (!$permitido) {
            throw new UsuarioSinPermisosException();
        }

        try {
            return $decoded;
        } catch (Exception $e) {
            throw new UsuarioSinPermisosException();
        }
    }

    /**
     * Static Method GetDecodeToken, obtiene el token decodificado
     * @param $token
     * @return object
     * @throws InvalidTokenException
     */
    static function GetDecodeToken($token) {
        // Validacion del token
        if (is_array($token)) {
            if (empty($token)) {
                throw new InvalidTokenException("Token no definido");
            }
            $token = $token[0];
        }

        // Decode del token
        try {
            $jwtProvider = new JWTProvider();
            $decoded = $jwtProvider->Decode($token);
        } catch (InvalidTokenException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (Exception $e) {
            throw new InvalidTokenException($e->getMessage());
        }

        return $decoded;
    }

    static function IsEDSuser($token) {
        // Validacion del token
        if (is_array($token)) {
            if (empty($token)) {
                throw new InvalidTokenException("Token no definido");
            }
            $token = $token[0];
        }

        // Decode del token
        try {
            $jwtProvider = new JWTProvider();
            $decoded = $jwtProvider->Decode($token);
        } catch (InvalidTokenException $e) {
            throw new InvalidTokenException($e->getMessage());
        } catch (Exception $e) {
            throw new InvalidTokenException($e->getMessage());
        }
        if ($decoded == null) {
            throw new InvalidTokenException();
        }

        // Validacion de permisos segun accion y empresa
        return $decoded->bid == 1;
    }

    static function generarPassword($nombreUsuario, $password) {
        return hash('sha256', $nombreUsuario . $password);
    }

}