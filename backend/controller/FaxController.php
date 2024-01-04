<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\FaxService;
use \utils\LogUtils;
use \dto\FaxDTO;
use \dto\ItemCreadoDTO;

/**
 * FAX API REST
 */
$app->group('/fax/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/fax/";

    $app->get($p.'{idEmpresa}', FaxController::class . ':getFaxPorEmpresa');
    $app->get($p.'{idEmpresa}/{id}', FaxController::class . ':getFaxPorId');
    $app->post($p.'crear', FaxController::class . ':crear');
    $app->post($p.'modificar', FaxController::class . ':modificar');
    $app->post($p.'eliminar/{idEmpresa}/{id}', FaxController::class . ':eliminar');

});

/**
 * Definicion de clase
 */
class FaxController extends \controller\AGenericController
{

    /*
     * FAX
     * Obtiene todos los faxes de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getFaxPorEmpresa($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener faxes", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            $newToken = "";

            $faxSrv = new FaxService();
            $fax = $faxSrv->getFax($request->getAttribute('idEmpresa'));
            $faxList = ControllerUtils::AsArrayList($fax);

            return (new OKResponse("OK", $faxList, $newToken))->GetResponse();
        });
    }

    /*
     * FAX
     * Obtiene un fax en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function getFaxPorId($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener fax", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            $newToken = "";

            $faxSrv = new FaxService();
            $fax = $faxSrv->getFax($request->getAttribute('idEmpresa'), $request->getAttribute('id'));
            $faxList = ControllerUtils::AsArrayList($fax);

            return (new OKResponse("OK", $faxList, $newToken))->GetResponse();
        });
    }

    /*
    * FAX
    * Crea un fax
    * Funcion: 'vaa.operar'
    */
    public function crear($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST guardar fax", basename(__FILE__), "guardar");

            $faxDTO = new FaxDTO();
            $faxDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $faxDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            $newToken = "";

            $faxSrv = new FaxService();
            $id = $faxSrv->guardar($faxDTO);
            $itemCreado = new ItemCreadoDTO($id);
            $itemCreadoList = ControllerUtils::AsArrayList($itemCreado);

            return (new OKResponse("OK", $itemCreadoList, $newToken))->GetResponse();
        });
    }

    /*
     * FAX
     * Modifica un fax
     * Funcion: 'vaa.operar'
     */
    public function modificar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST guardar fax", basename(__FILE__), "guardar");

            $faxDTO = new FaxDTO();
            $faxDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $faxDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            $newToken = "";

            $faxSrv = new FaxService();
            $faxSrv->guardar($faxDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * FAX
     * Elimina un fax
     * Funcion: 'vaa.operar'
     */
    public function eliminar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar fax", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            $newToken = "";

            $faxSrv = new FaxService();
            $faxSrv->eliminar($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

}