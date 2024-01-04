<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\CheckService;
use \utils\LogUtils;
use \dto\CheckArchivoDTO;

/**
 * API REST
 */
$app->group('/check/', function () use ($app) {
	
	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/check/";
	
    $app->get($p.'{idEmpresa}/archivos/departamentos', CheckController::class . ':checkArchivosDepartamentos');
    $app->get($p.'{idEmpresa}/archivos/personas', CheckController::class . ':checkArchivosPersonas');

});

/**
 * Definicion de clase
 */
class CheckController extends \controller\AGenericController
{

    /*
     * CHECK
     * Realiza un chequeo de los archivos de voz de los departamentos
     * Funcion: 'vaa.operar'
     */
    public function checkArchivosDepartamentos($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST check archivos departamentos", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $checkSrv = new CheckService();
            $checkArchivo = $checkSrv->checkArchivosDepartamentos($request->getAttribute('idEmpresa'));
            $checkArchivoList = ControllerUtils::AsArrayList($checkArchivo);

            return (new OKResponse("OK", $checkArchivoList, $newToken))->GetResponse();
        });
    }

    /*
     * CHECK
     * Realiza un chequeo de los archivos de voz de las personas
     * Funcion: 'vaa.operar'
     */
    public function checkArchivosPersonas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST check archivos departamentos", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $checkSrv = new CheckService();
            $checkArchivo = $checkSrv->checkArchivosPersonas($request->getAttribute('idEmpresa'));
            $checkArchivoList = ControllerUtils::AsArrayList($checkArchivo);

            return (new OKResponse("OK", $checkArchivoList, $newToken))->GetResponse();
        });
    }

}