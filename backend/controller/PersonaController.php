<?php

use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \utils\LogUtils;
use \serviceimpl\PersonaService;
use \dto\PersonaDTO;
use \serviceimpl\ParametroService;
use \serviceimpl\EmpresaService;
use \dto\ItemCreadoDTO;

/**
 * PERSONA API REST
 */
$app->group('/persona/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/persona/";

    $app->get($p.'{idEmpresa}', PersonaController::class . ':getPersonaPorEmpresa');
    $app->post($p.'{idEmpresa}/min', PersonaController::class . ':getPersonaPorEmpresaMin');
    $app->post($p.'{idEmpresa}/min/no-cast', PersonaController::class . ':getPersonaPorEmpresaMinNoCast');
    $app->get($p.'{idEmpresa}/{id}', PersonaController::class . ':getPersonaPorId');
    $app->post($p.'crear', PersonaController::class . ':crear');
    $app->post($p.'modificar', PersonaController::class . ':modificar');
    $app->post($p.'eliminar/{idEmpresa}/{id}', PersonaController::class . ':eliminar');
    $app->get($p.'{idEmpresa}/get/secretaria', PersonaController::class . ':getSecretariaPorEmpresa');
    $app->get($p.'{idEmpresa}/get/secretaria-transferible', PersonaController::class . ':getSecretariasYTransferiblesPorEmpresa');
    $app->get($p.'{idEmpresa}/get/total', PersonaController::class . ':getCantidadPersonasPorEmpresa');
});

/**
 * Definicion de clase
 */
class PersonaController extends \controller\AGenericController
{
    /*
     * PERSONA
     * Obtiene todas las personas de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getPersonaPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener personas", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $persona = $personaSrv->getPersona($request->getAttribute('idEmpresa'));
            $personaList = ControllerUtils::AsArrayList($persona);

            return (new OKResponse("OK", $personaList, $newToken))->GetResponse();
        });
    }

    /*
     * PERSONA MIN
     * Obtiene todas las personas de una empresa (en formato min)
     * Funcion: 'vaa.operar'
     */
    public function getPersonaPorEmpresaMin($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener personas min", basename(__FILE__), "post");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $dep_id = null;
            $filters = $request->getParsedBody();
            if ($filters != null && count($filters) > 0) {
                if (isset($filters['dep_id'])) {
                    $dep_id = $filters['dep_id'];
                }
            }

            $personaSrv = new PersonaService();
            $persona = $personaSrv->getPersona($request->getAttribute('idEmpresa'), null, $dep_id, false, true);
            $personaList = ControllerUtils::AsArrayList($persona);

            return (new OKResponse("OK", $personaList, $newToken))->GetResponse();
        });
    }

    /*
     * PERSONA MIN NO-CAST
     * Obtiene todas las personas de una empresa (en formato min y sin castear)
     * Funcion: 'vaa.operar'
     */
    public function getPersonaPorEmpresaMinNoCast($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener personas min no-cast", basename(__FILE__), "post");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $personas = $personaSrv->getPersonaMinNoCast($request->getAttribute('idEmpresa'));
            // $personaList = ControllerUtils::AsArrayList($persona);

            return (new OKResponse("OK", $personas, $newToken))->GetResponse();
        });
    }

    /*
     * PERSONA
     * Obtiene una persona en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function getPersonaPorId($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener persona", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $persona = $personaSrv->getPersona($request->getAttribute('idEmpresa'), $request->getAttribute('id'), null, true);
            $personaList = ControllerUtils::AsArrayList($persona);

            return (new OKResponse("OK", $personaList, $newToken))->GetResponse();
        });
    }

    /*
     * PERSONA
     * Crea una persona
     * Funcion: 'vaa.operar'
     */
    public function crear($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST insert persona", basename(__FILE__), "insert");

            $personaDTO = new PersonaDTO($request->getParsedBody());
            $idEmpresa = $personaDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }
            $idDepartamento = $personaDTO->getPhbDepId();
            if ($idDepartamento == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $countPersonas = $personaSrv->getCountPersonas($personaDTO->getBusinessId());
            $empresaSrv = new EmpresaService();
            $empresaDTO = $empresaSrv->getEmpresa($personaDTO->getBusinessId());
            if ($countPersonas + 1 > $empresaDTO->getCant_max_personas()) {
                return (new ErrorResponse("Excede el límite máximo de " . $empresaDTO->getCant_max_personas() . " personas. Contactarse con EDS."))->GetResponse();
            }
            $id = $personaSrv->guardarPersona($personaDTO);
            $itemCreado = new ItemCreadoDTO($id);
            $itemCreadoList = ControllerUtils::AsArrayList($itemCreado);

            return (new OKResponse("OK", $itemCreadoList, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Modifica una persona
     * Funcion: 'vaa.operar'
     */
    public function modificar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST update voice mail", basename(__FILE__), "update");

            $personaDTO = new PersonaDTO($request->getParsedBody());
            $idEmpresa = $personaDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $personaSrv->guardarPersona($personaDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Elimina una persona
     * Funcion: 'vaa.operar'
     */
    public function eliminar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST eliminar persona", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $personaSrv->eliminarPersona($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }
    
    /*
     * PERSONA
     * Obtiene todas las secretarias de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getSecretariaPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener secretarias", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $persona = $personaSrv->getSecretaria($request->getAttribute('idEmpresa'));
            $personaList = ControllerUtils::AsArrayList($persona);

            return (new OKResponse("OK", $personaList, $newToken))->GetResponse();
        });
    }

    /*
     * PERSONA
     * Obtiene todas las secretarias y personas transferibles de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getSecretariasYTransferiblesPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener secretarias y personas transferibles", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $secretarias = $personaSrv->getSecretaria($request->getAttribute('idEmpresa'));
            $transferibles = $personaSrv->getTransferibles($request->getAttribute('idEmpresa'));
            $secretariasList = ControllerUtils::AsArrayList($secretarias);
            $transferiblesList = ControllerUtils::AsArrayList($transferibles);
            $aux = array("secretarias"=>$secretariasList, "transferibles"=>$transferiblesList);

            return (new OKResponse("OK", $aux, $newToken))->GetResponse();
        });
    }

    /*
     * CANTIDAD DE PERSONAS
     * Obtiene el total de personas de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getCantidadPersonasPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener cantidad de personas", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $personaSrv = new PersonaService();
            $countPersonas = $personaSrv->getCountPersonas($request->getAttribute('idEmpresa'));

            return (new OKResponse("OK", $countPersonas, $newToken))->GetResponse();
        });
    }

}