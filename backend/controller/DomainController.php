<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\DomainService;
use \utils\LogUtils;
use \dto\DomainDTO;

/**
 * DOMAIN API REST
 */
$app->group('/domain/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/domain/";

    $app->get($p.'{idEmpresa}', DomainController::class . ':getDomainPorEmpresa');
    $app->get($p.'{idEmpresa}/{id}', DomainController::class . ':getDomainPorId');
    $app->post($p.'crear', DomainController::class . ':crear');
    $app->post($p.'modificar', DomainController::class . ':modificar');
    $app->post($p.'eliminar/{idEmpresa}/{id}', DomainController::class . ':eliminar');

});

/**
 * Definicion de clase
 */
class DomainController extends \controller\AGenericController
{

    public function getDomainPorEmpresa($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener domains", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $domainSrv = new DomainService();
            $domain = $domainSrv->getDomain($request->getAttribute('idEmpresa'));
            $domainList = ControllerUtils::AsArrayList($domain);

            return (new OKResponse("OK", $domainList, $newToken))->GetResponse();
        });
    }

    public function getDomainPorId($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener domain", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $domainSrv = new DomainService();
            $domain = $domainSrv->getDomain($request->getAttribute('idEmpresa'), $request->getAttribute('id'));
            $domainList = ControllerUtils::AsArrayList($domain);

            return (new OKResponse("OK", $domainList, $newToken))->GetResponse();
        });
    }

    public function crear($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST guardar domain", basename(__FILE__), "guardar");

            $domainDTO = new DomainDTO();
            $domainDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $domainDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $domainSrv = new DomainService();
            $domainSrv->guardar($domainDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    public function modificar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST guardar domain", basename(__FILE__), "guardar");

            $domainDTO = new DomainDTO();
            $domainDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $domainDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $domainSrv = new DomainService();
            $domainSrv->guardar($domainDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    public function eliminar($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar domain", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $domainSrv = new DomainService();
            $domainSrv->eliminar($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

}