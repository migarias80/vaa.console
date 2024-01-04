<?php

use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \serviceimpl\UsuarioService;
use \serviceimpl\EmpresaService;
use \utils\LogUtils;
use \dto\EmpresaDTO;
use \responses\ErrorResponse;

/**
 * EMPRESA API REST
 */
$app->group('/empresa/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/empresa/";

    $app->get($p.'usuarios/{idEmpresa}', EmpresaController::class . ':getUsuariosPorEmpresa');
    $app->get($p.'url/{urlName}', EmpresaController::class . ':getEmpresaPorURL');
    $app->get($p.'', EmpresaController::class . ':getEmpresa');
    $app->get($p.'get/{idEmpresa}', EmpresaController::class . ':getEmpresaPorId');
    $app->post($p.'crear', EmpresaController::class . ':crear');
    $app->post($p.'modificar', EmpresaController::class . ':modificar');
    $app->post($p.'set-image', EmpresaController::class . ':setImage');
    $app->get($p.'evaluacion/{idEmpresa}', EmpresaController::class . ':getEvaluacion');
    $app->post($p.'set-regex', EmpresaController::class . ':setRegEx');
    $app->post($p.'set-regex-ext', EmpresaController::class . ':setRegExExt');
    $app->post($p.'set-output-route', EmpresaController::class . ':setOutputRoute');
    $app->get($p.'evaluacion/{idEmpresa}/dnis/{nroEval}', EmpresaController::class . ':getEvaluacionDNIS');
    $app->get($p.'status/{idEmpresa}', EmpresaController::class . ':getEmpresaStatus');
    $app->get($p.'eliminar/{idEmpresa}', EmpresaController::class . ':eliminarEmpresa');
    $app->post($p.'set-configuracion-general', EmpresaController::class . ':setConfiguracionGeneral');
    $app->post($p.'set-tts-mode', EmpresaController::class . ':setTTSMode');
    $app->get($p.'mhc/rutas/{idEmpresa}', EmpresaController::class . ':getRutasMHCDeEmpresa');

    // TODO: Obsoleto
    $app->post($p.'set-cant-max-personas', EmpresaController::class . ':setCantMaxPersonas');
    $app->post($p.'set-cant-max-departamentos', EmpresaController::class . ':setCantMaxDepartamentos');
    $app->post($p.'set-cant-max-lineas', EmpresaController::class . ':setCantMaxLineas');
    $app->post($p.'set-contacto', EmpresaController::class . ':setContacto');
    $app->post($p.'set-notas', EmpresaController::class . ':setNotas');
    $app->post($p.'set-enabled', EmpresaController::class . ':setEnabled');
    $app->post($p.'set-disabled', EmpresaController::class . ':setDisabled');
});

/**
 * Definicion de clase
 */
class EmpresaController extends controller\AGenericController
{

    /*
     * EMPRESA
     * Devuelve todos los usuarios de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getUsuariosPorEmpresa($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $usuarioSrv = new UsuarioService();
            $usuarios = $usuarioSrv->getUsuariosByIdEmpresa($request->getAttribute('idEmpresa'));
            $usuarioList = ControllerUtils::AsArrayList($usuarios);

            return (new OKResponse("OK", $usuarioList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Devuelve los datos de una empresa en base al nombre URL
     * Funcion: 'PERMIT-ALL'
     */
    public function getEmpresaPorURL($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener empresa por url", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("urlName"));

            $newToken = $request->getHeader("Authorization");
            
            try {
                $empresaSrv = new EmpresaService();
                $empresas = $empresaSrv->getEmpresaPorURL($request->getAttribute('urlName'));
                $empresasList = ControllerUtils::AsArrayList($empresas);
            } catch (Exception $e) {
                if ($e instanceof PDOException) {
                    LogUtils::ERROR($e->getMessage(), __FILE__);
                    return (new ErrorResponse("Ocurrio un error al conectarse a la base de datos", CODE_ERROR_INESPERADO))->GetResponse();
                }
            }
            return (new OKResponse("OK", $empresasList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Devuelve todas las empresa
     * Funcion: 'vaa.operar'
     */
    public function getEmpresa($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresas = $empresaSrv->getEmpresa();
            $empresasList = ControllerUtils::AsArrayList($empresas);

            return (new OKResponse("OK", $empresasList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Obtiene una empresa en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function getEmpresaPorId($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener empresa", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresas = $empresaSrv->getEmpresa($request->getAttribute('idEmpresa'));
            $empresasList = ControllerUtils::AsArrayList($empresas);

            return (new OKResponse("OK", $empresasList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Guarda una empresa
     * Funcion: 'vaa.operar'
     */
    public function crear($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO($request->getParsedBody());

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->guardar($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza una empresa
     * Funcion: 'vaa.operar'
     */
    public function modificar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->guardar($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza una imagen de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setImage($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setImage($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Devuelve una evaluacion de los datos cargados de la empresa
     * Funcion: 'vaa.operar'
     */
    public function getEvaluacion($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $evaluacionEmpresa = $empresaSrv->getEvaluacion($request->getAttribute('idEmpresa'));
            $evaluacionEmpresaList = ControllerUtils::AsArrayList($evaluacionEmpresa);

            return (new OKResponse("OK", $evaluacionEmpresaList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza el dnis regex de la guia interna de una empresa 
     * Funcion: 'vaa.operar'
     */
    public function setRegEx($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setRegEx($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza el dnis regex de la guia externa de una empresa 
     * Funcion: 'vaa.operar'
     */
    public function setRegExExt($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setRegExExt($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza el output route de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setOutputRoute($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setOutputRoute($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Devuelve las empresas que matchean con el DNIS recibido
     * Funcion: 'vaa.operar'
     */
    public function getEvaluacionDNIS($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("nroEval"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $evaluacionEmpresa = $empresaSrv->getEvaluacionDNIS($request->getAttribute('nroEval'));
            $evaluacionEmpresaList = ControllerUtils::AsArrayList($evaluacionEmpresa);

            return (new OKResponse("OK", $evaluacionEmpresaList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Devuelve el status general de la empresa, parametros principales, cantidad de personas y deptos
     * Funcion: 'vaa.operar'
     */
    public function getEmpresaStatus($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresas = $empresaSrv->getEmpresa();
            $empresasList = ControllerUtils::AsArrayList($empresas);

            return (new OKResponse("OK", $empresasList, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Elimina una empresa en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function eliminarEmpresa($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar empresa", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresas = $empresaSrv->eliminar($request->getAttribute('idEmpresa'));

            return (new OKResponse("OK", [], $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza la cantidad maxima de personas de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setCantMaxPersonas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setCantMaxPersonas($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza la cantidad maxima de departamentos de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setCantMaxDepartamentos($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setCantMaxDepartamentos($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza la cantidad maxima de lineas de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setCantMaxLineas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setCantMaxLineas($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza el contacto de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setContacto($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setContacto($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza las notas de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setNotas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            // Siempre y cuando se trate de un modo de consola MULTI y sea EDS
            if ($decodedToken->bid != 1) {
                throw new UsuarioSinPermisosException();
            }

            $empresaSrv = new EmpresaService();
            $empresaSrv->setNotas($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Habilita una empresa
     * Funcion: 'vaa.operar'
     */
    public function setEnabled($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            // Siempre y cuando se trate de un modo de consola MULTI y sea EDS
            if ($decodedToken->bid != 1) {
                throw new UsuarioSinPermisosException();
            }

            $empresaSrv = new EmpresaService();
            $empresaSrv->setEnabled($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Deshabilita una empresa
     * Funcion: 'vaa.operar'
     */
    public function setDisabled($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            // Siempre y cuando se trate de un modo de consola MULTI y sea EDS
            if ($decodedToken->bid != 1) {
                throw new UsuarioSinPermisosException();
            }

            $empresaSrv = new EmpresaService();
            $empresaSrv->setDisabled($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Actualiza la configuracion general de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setConfiguracionGeneral($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setConfiguracionGeneral($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Define el valor del TTS Mode de una empresa
     * Funcion: 'vaa.operar'
     */
    public function setTTSMode($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){;
            $body = $request->getBody();
            $data = json_decode($body, true);

            $empresaDTO = new \dto\EmpresaDTO();
            $empresaDTO->constructFromJSON($data);

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar", 1);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresaSrv->setTTSMode($empresaDTO);
            return (new OKResponse("OK", CODE_OK, $newToken))->GetResponse();
        });
    }

    /*
     * EMPRESA
     * Obtiene las rutas configuradas en el mhc de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getRutasMHCDeEmpresa($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener rutas configuradas en el archivo mhc", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $empresaSrv = new EmpresaService();
            $empresas = $empresaSrv->getRutasMHCDeEmpresa($request->getAttribute('idEmpresa'));
            $empresasList = ControllerUtils::AsArrayList($empresas);

            return (new OKResponse("OK", $empresasList, $newToken))->GetResponse();
        });
    }

}