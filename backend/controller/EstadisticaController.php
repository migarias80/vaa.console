<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\EstadisticaService;
use \utils\LogUtils;
use \dto\EstadisticaDTO;

/**
 * API REST
 */
$app->group('/estadistica/', function () use ($app) {
	
	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/estadistica/";

    $app->get($p.'llamadas/{idEmpresa}/{fechaDesde}/{fechaHasta}', EstadisticaController::class . ':getLlamadas');
    $app->get($p.'troncales/{idEmpresa}/{fechaDesde}/{fechaHasta}', EstadisticaController::class . ':getTroncales');
    $app->get($p.'troncales-maximos/{idEmpresa}/{fechaDesde}/{fechaHasta}', EstadisticaController::class . ':getTroncalesMaximos');
    $app->get($p.'detalle-troncal/{idEmpresa}/{fecha}', EstadisticaController::class . ':getDetalleTroncal');

});

/**
 * Definicion de clase
 */
class EstadisticaController extends \controller\AGenericController
{

    /*
     * ESTADISTICA
     * Obtiene la cantidad de llamadas en un rango de fechas
     * Funcion: 'vaa.operar'
     */
    public function getLlamadas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST getLlamadas", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaDesde"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaHasta"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $estadisticaSrv = new EstadisticaService();
            $estadistica = $estadisticaSrv->getLlamadas($request->getAttribute('idEmpresa'), $request->getAttribute('fechaDesde'), $request->getAttribute('fechaHasta'));
            $estadisticaList = ControllerUtils::AsArrayList($estadistica);

            return (new OKResponse("OK", $estadisticaList, $newToken))->GetResponse();
        });
    }

    /*
     * ESTADISTICA
     * Obtiene el uso de cada troncal en un rango de fechas
     * Funcion: 'vaa.operar'
     */
    public function getTroncales($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST getTroncalesMaximos", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaDesde"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaHasta"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $estadisticaSrv = new EstadisticaService();
            $estadistica = $estadisticaSrv->getTroncales($request->getAttribute('idEmpresa'), $request->getAttribute('fechaDesde'), $request->getAttribute('fechaHasta'));
            $estadisticaList = ControllerUtils::AsArrayList($estadistica);

            return (new OKResponse("OK", $estadisticaList, $newToken))->GetResponse();
        });
    }

    /*
     * ESTADISTICA
     * Obtiene el máximo de troncales usados por dia (en un rango de fechas)
     * Funcion: 'vaa.operar'
     */
    public function getTroncalesMaximos($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST getTroncalesMaximos", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaDesde"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fechaHasta"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $estadisticaSrv = new EstadisticaService();
            $estadistica = $estadisticaSrv->getTroncalesMaximos($request->getAttribute('idEmpresa'), $request->getAttribute('fechaDesde'), $request->getAttribute('fechaHasta'));
            $estadisticaList = ControllerUtils::AsArrayList($estadistica);

            return (new OKResponse("OK", $estadisticaList, $newToken))->GetResponse();
        });
    }

    /*
     * ESTADISTICA
     * Obtiene cuantos minutos fue usado en un día cada troncal
     * Funcion: 'vaa.operar'
     */
    public function getDetalleTroncal($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST getDetalleTroncal", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));
            ControllerUtils::VerifyMandatoryRequest($request, array("fecha"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $estadisticaSrv = new EstadisticaService();
            $estadistica = $estadisticaSrv->getDetalleTroncal($request->getAttribute('idEmpresa'), $request->getAttribute('fecha'));
            $estadisticaList = ControllerUtils::AsArrayList($estadistica);

            return (new OKResponse("OK", $estadisticaList, $newToken))->GetResponse();
        });
    }

}