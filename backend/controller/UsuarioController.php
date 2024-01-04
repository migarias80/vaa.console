<?php

use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \utils\LogUtils;
use \responses\ErrorResponse;
use \responses\OKResponse;
use \exception\MandatoryRequestException;
use \dto\LoginDTO;
use \serviceimpl\UsuarioService;
use \dto\UsuarioDTO;
use \model\UsuarioPerfil;
use \exception\UsuarioSinPermisosException;

/**
 * USUARIO API REST
 */
$app->group('/usuario/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/usuario/";
	
    $app->post($p.'login', UsuarioController::class . ':login');
    $app->get($p.'token', UsuarioController::class . ':getDecodeToken');
    $app->post($p.'update-last-access', UsuarioController::class . ':updateLastAccess');
    $app->get($p.'get', UsuarioController::class . ':getUsuarioByToken');
    $app->get($p.'{idEmpresa}/{name}', UsuarioController::class . ':getUsuarioPorNombre');
    $app->post($p.'get-last-access/{idEmpresa}', UsuarioController::class . ':getLastAccess');
    $app->get($p.'get-historial/{idEmpresa}/{id}', UsuarioController::class . ':getHistorial');
    $app->get($p.'get-all-access/{idEmpresa}/{id}', UsuarioController::class . ':getAccesos');
    $app->post($p.'crear', UsuarioController::class . ':crear');
    $app->post($p.'modificar', UsuarioController::class . ':modificar');
    $app->post($p.'modificar-mis-datos', UsuarioController::class . ':modificarMyData');
    $app->post($p.'deshabilitar/{idEmpresa}/{id}', UsuarioController::class . ':deshabilitar');
    $app->post($p.'habilitar/{idEmpresa}/{id}', UsuarioController::class . ':habilitar');
    $app->post($p.'password/{idEmpresa}', UsuarioController::class . ':updateMyPassword');
    $app->post($p.'password-set/{idEmpresa}', UsuarioController::class . ':setPassword');
    $app->post($p.'password-set-sa/{idEmpresa}', UsuarioController::class . ':setSAPassword');
    $app->post($p.'password-set-sa-admin/{idEmpresa}', UsuarioController::class . ':setSAAdminPassword');
    $app->post($p.'eliminar/{idEmpresa}/{id}', UsuarioController::class . ':eliminar');

});

/**
 * Definicion de clase
 */
class UsuarioController extends \controller\AGenericController
{
	
    /*
     * USUARIO
     * Login de usuario
     * Funcion: 'PERMIT-ALL'
     */
    public function login($request, $response, $args) {
        try {
            LogUtils::INFO("REST login de usuario", basename(__FILE__), "login");

            $body = $request->getBody();
            $data = json_decode($body, true);

            $usuario = new LoginDTO($data);
            $usuarioSrv = new UsuarioService();
            $result = $usuarioSrv->login($usuario);
            if ($result["status"]) {
                $idUsuario = $result["message"];
                $token = SecurityUtils::GetToken($idUsuario);
                if ($token) {

                    // Actualizacion de ultimo acceso
                    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                    } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    if ($ip == "::1") {
                        $ip = "LOCALHOST";
                    }

                    $decodedToken = SecurityUtils::GetDecodeToken($token);
                    $usuarioUltimoAccesoDTO = new \dto\UsuarioUltimoAccesoDTO();
                    $usuarioUltimoAccesoDTO->setBusinessId($decodedToken->bid);
                    $usuarioUltimoAccesoDTO->setUserId($decodedToken->uid);
                    $usuarioUltimoAccesoDTO->setIpAddress($ip);
                    $usuarioSrv->updateLastAccess($usuarioUltimoAccesoDTO);

                    return (new OKResponse("OK", CODE_OK, $token))->GetResponse();
                } else {
                    return (new ErrorResponse("Error al generar el token"))->GetResponse();
                }
            } else if ($result["message"] != null) {
                return (new OKResponse($result["message"], CODE_ERROR_AUTENTICACION))->GetResponse();
            } else {
                return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO))->GetResponse();
            }
        }catch (Exception $e) {
            LogUtils::ERROR($e->getMessage(), __FILE__, "login");
            return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO, $e))->GetResponse();
        }
    }

    /*
     * USUARIO
     * Obtiene todos los datos de un usuario en base a su nombre y una empresa
     * Funcion: 'vaa.operar'
     */
    public function getUsuarioPorNombre($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "name"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioDTO = $usuarioSrv->getUsuarioByNombreYEmpresa($request->getAttribute('name'), $request->getAttribute('idEmpresa'));
            $usuarioList = ControllerUtils::AsArrayList($usuarioDTO);

            return (new OKResponse("OK", $usuarioList, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Crea un usuario
     * Funcion: 'usuarios.crear'
     */
    function crear($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            $usuarioDTO = new \dto\UsuarioDTO();
            $usuarioDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $usuarioDTO->getIdEmpresa();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioDTO->setLastEditUserId($decodedToken->uid);
            $usuarioSrv->nuevo($usuarioDTO);
            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Modifica un usuario
     * Funcion: 'usuarios.crear'
     */
    function modificar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            return $this->excecute($request, $response, function($request, $response){
                LogUtils::INFO("REST modificar un usuario", basename(__FILE__), "guardar");

                $usuarioDTO = new UsuarioDTO();
                $usuarioDTO->constructFromArray($request->getParsedBody());
                $idEmpresa = $usuarioDTO->getIdEmpresa();
                if ($idEmpresa == null) {
                    throw new MandatoryRequestException();
                }

                $decodedToken = SecurityUtils::CheckPermissions(
                    $request->getHeader("Authorization"),
                    "usuarios.crear",
                    $idEmpresa);
                // $newToken = SecurityUtils::GetToken($decodedToken->uid);
                $newToken = "";

                $usuarioSrv = new UsuarioService();
                $usuarioDTO->setLastEditUserId($decodedToken->uid);
                $usuarioSrv->modificar($usuarioDTO);

                return (new OKResponse("OK", null, $newToken))->GetResponse();
            });
        });
    }

    /*
     * USUARIO
     * Modifica mis datos de usuario (obtenine el id y su empresa del token)
     * Funcion: 'vaa.operar'
     */
    function modificarMyData($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            return $this->excecute($request, $response, function($request, $response){
                LogUtils::INFO("REST modificar mis datos de usuario", basename(__FILE__), "guardar");

                $decodedToken = SecurityUtils::CheckPermissions(
                    $request->getHeader("Authorization"),
                    "vaa.operar");
                // $newToken = SecurityUtils::GetToken($decodedToken->uid);
                $newToken = "";

                $usuarioDTO = new UsuarioDTO();
                $usuarioDTO->constructFromArray($request->getParsedBody());
                $usuarioDTO->setIdEmpresa($decodedToken->bid);
                $usuarioDTO->setId($decodedToken->uid);

                if ($decodedToken->uid != $usuarioDTO->getId()) {
                    throw new UsuarioSinPermisosException();
                }

                $usuarioSrv = new UsuarioService();
                $usuarioDTO->setLastEditUserId($decodedToken->uid);
                $usuarioSrv->modificarMisDatos($usuarioDTO);

                return (new OKResponse("OK", null, $newToken))->GetResponse();
            });
        });
    }

    /*
     * USUARIO
     * Obtiene los datos del usuario en base al token
     * Funcion: 'PERMIT-ALL'
     */
    function getDecodeToken($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            $decodedToken = SecurityUtils::GetDecodeToken($request->getHeader("Authorization"));

            $newToken = $request->getHeader("Authorization");

            return (new OKResponse("OK", $decodedToken, $newToken))->GetResponse();
        });
    }

    /*
    * USUARIO
    * Deshabilita un usuario
    * Funcion: 'usuarios.crear'
    */
    public function deshabilitar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST deshabilitar usuario", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioSrv->deshabilitar($request->getAttribute('idEmpresa'), $request->getAttribute('id'), $decodedToken->uid);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }
    
    /*
    * USUARIO
    * Habilita un usuario
    * Funcion: 'usuarios.crear'
    */
    public function habilitar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST habilitar usuario", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioSrv->habilitar($request->getAttribute('idEmpresa'), $request->getAttribute('id'), $decodedToken->uid);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
    * USUARIO
    * Deshabilita un usuario
    * Funcion: 'usuarios.crear'
    */
    public function eliminar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar usuario", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioSrv->eliminar($request->getAttribute('idEmpresa'), $request->getAttribute('id'), $decodedToken->uid);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Actualiza la fecha de ultimo acceso
     * Funcion: 'vaa.operar'
     */
    function updateLastAccess($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST set ultimo acceso del usuario", basename(__FILE__), "eliminar");

            $body = $request->getBody();
            $data = json_decode($body, true);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            // Actualizacion de ultimo acceso
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            if ($ip == "::1") {
                $ip = "LOCALHOST";
            }

            $usuarioUltimoAccesoDTO = new \dto\UsuarioUltimoAccesoDTO();
            $usuarioUltimoAccesoDTO->constructFromArray($data);
            $usuarioUltimoAccesoDTO->setUserId($decodedToken->uid);
            $usuarioUltimoAccesoDTO->setIpAddress($ip);

            $usuarioSrv = new UsuarioService();
            $usuarioSrv->updateLastAccess($usuarioUltimoAccesoDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Obtiene sus datos en base al token
     * Funcion: 'PERMIT-ALL'
     */
    function getUsuarioByToken($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener datos del usuario en base al token", basename(__FILE__), "eliminar");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioDTO = $usuarioSrv->getUsuarioById($decodedToken->uid);
            $usuarioList = ControllerUtils::AsArrayList($usuarioDTO);

            return (new OKResponse("OK", $usuarioList, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Actualiza el password del usuario actual (requiere del password anterior y que el usuario del token sea el mismo que se esta actualizando)
     * Funcion: 'vaa.operar'
     */
    function updateMyPassword($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST set ultimo acceso del usuario", basename(__FILE__), "eliminar");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioDTO = new \dto\UsuarioDTO();
            $usuarioDTO->constructFromArray($request->getParsedBody());
            $passwordAnterior = ($request->getParsedBody())['old_password'];
            $usuarioDTO->setIdEmpresa($request->getAttribute('idEmpresa'));

            $usuarioSrv = new UsuarioService();
            $usuarioDTO->setLastEditUserId($decodedToken->uid);
            $usuarioSrv->updateMyPassword($usuarioDTO, $passwordAnterior);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Actualiza el password de un operador o administrador
     * Esta accion requiere que el perfil a actualizar no sea SA y que provenga de un perfil SA o ADMINISTRADOR
     * Funcion: 'usuarios.crear'
     */
    function setPassword($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST set password", basename(__FILE__), "setPassword");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioDTO = new \dto\UsuarioDTO();
            $usuarioDTO->constructFromArray($request->getParsedBody());

            $usuarioSrv = new UsuarioService();
            $usuarioDTO->setLastEditUserId($decodedToken->uid);
            $usuarioSrv->setPassword($usuarioDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Actualiza el password de un SA (requiere que quien invoque al servicio sea un usuario SA)
     * Funcion: 'usuarios.crear'
     */
    function setSAPassword($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST set password sa", basename(__FILE__), "setSAPassword");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));

            if ($decodedToken->pid != UsuarioPerfil::SA) {
                throw new UsuarioSinPermisosException();
            }
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioDTO = new \dto\UsuarioDTO();
            $usuarioDTO->constructFromArray($request->getParsedBody());

            $usuarioSrv = new UsuarioService();
            $usuarioDTO->setLastEditUserId($decodedToken->uid);
            $usuarioSrv->setSAPassword($usuarioDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Actualiza el password de un SA (requiere que quien invoque al servicio sea un usuario SA o Admin)
     * Funcion: 'usuarios.crear'
     */
    function setSAAdminPassword($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST set password operador", basename(__FILE__), "setSAPassword");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "usuarios.crear",
                $request->getAttribute('idEmpresa'));

            if ($decodedToken->pid != UsuarioPerfil::SA && $decodedToken->pid != UsuarioPerfil::ADMINISTRADOR) {
                throw new UsuarioSinPermisosException();
            }
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioDTO = new \dto\UsuarioDTO();
            $usuarioDTO->constructFromArray($request->getParsedBody());

            $usuarioSrv = new UsuarioService();
            $usuarioDTO->setLastEditUserId($decodedToken->uid);
            $usuarioSrv->setSAPassword($usuarioDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Obtiene la fecha de ultimo acceso de un usuario a una empresa
     * Funcion: 'vaa.operar'
     */
    function getLastAccess($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST get ultimo acceso del usuario", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioUltimoAccesoDTO = $usuarioSrv->getLastAccess($decodedToken->uid, $request->getAttribute('idEmpresa'));
            $usuarioUltimoAccesoList = null;
            if ($usuarioUltimoAccesoDTO != null) {
                $usuarioUltimoAccesoList = ControllerUtils::AsArrayList($usuarioUltimoAccesoDTO);
            }

            return (new OKResponse("OK", $usuarioUltimoAccesoList, $newToken))->GetResponse();
        });
    }

    /*
     * USUARIO
     * Obtiene el historial de un usuario
     * Funcion: 'vaa.operar'
     */
    function getHistorial($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST get historial del usuario", basename(__FILE__), "getHistorial");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioAUDTO = $usuarioSrv->getHistorial($request->getAttribute('id'), $request->getAttribute('idEmpresa'));
            $usuarioAUList = ControllerUtils::AsArrayList($usuarioAUDTO);

            return (new OKResponse("OK", $usuarioAUList, $newToken))->GetResponse();
        });
    }


    /*
     * USUARIO
     * Obtiene el historial de un usuario
     * Funcion: 'vaa.operar'
     */
    function getAccesos($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST get accesos del usuario", basename(__FILE__), "getAccesos");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarioAUDTO = $usuarioSrv->getAllAccess($request->getAttribute('id'), $request->getAttribute('idEmpresa'));
            $usuarioAUList = ControllerUtils::AsArrayList($usuarioAUDTO);

            return (new OKResponse("OK", $usuarioAUList, $newToken))->GetResponse();
        });
    }
}