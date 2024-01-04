<?php

use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\VoiceMailService;
use \utils\LogUtils;
use \dto\VoiceMailDTO;
use \serviceimpl\DepartmentService;
use \dto\DepartmentDTO;
use \serviceimpl\ParametroService;
use \serviceimpl\EmpresaService;
use \dto\ItemCreadoDTO;

/**
 * DEPARTAMENTO API REST
 */
$app->group('/department/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/department/";

    $app->get($p.'{idEmpresa}', DepartamentoController::class . ':getDepartmentPorEmpresa');
    $app->get($p.'{idEmpresa}/min', DepartamentoController::class . ':getDepartmentPorEmpresaMin');
    $app->get($p.'{idEmpresa}/{id}', DepartamentoController::class . ':getDepartmentPorId');
    $app->post($p.'crear', DepartamentoController::class . ':crear');
    $app->post($p.'modificar', DepartamentoController::class . ':modificar');
    $app->post($p.'eliminar/{idEmpresa}/{id}', DepartamentoController::class . ':eliminar');
    $app->get($p.'{idEmpresa}/get/total', DepartamentoController::class . ':getCantidadDepartmentPorEmpresa');

});

/**
 * Definicion de clase
 */
class DepartamentoController extends \controller\AGenericController
{
    /*
     * DEPARTMENT
     * Obtiene todos los departments de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getDepartmentPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener departments", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $department = $departmentSrv->getDepartment($request->getAttribute('idEmpresa'));
            $departmentList = ControllerUtils::AsArrayList($department);

            return (new OKResponse("OK", $departmentList, $newToken))->GetResponse();
        });
    }

    /*
     * DEPARTMENT MIN
     * Obtiene todos los departments de una empresa (en formato min)
     * Funcion: 'vaa.operar'
     */
    public function getDepartmentPorEmpresaMin($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener departments min", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $department = $departmentSrv->getDepartment($request->getAttribute('idEmpresa'), null, null, false, true);
            $departmentList = ControllerUtils::AsArrayList($department);

            return (new OKResponse("OK", $departmentList, $newToken))->GetResponse();
        });
    }

    /*
     * DEPARTMENT
     * Obtiene un department en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function getDepartmentPorId($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener department", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $department = $departmentSrv->getDepartment($request->getAttribute('idEmpresa'), $request->getAttribute('id'), null, true);
            $departmentList = ControllerUtils::AsArrayList($department);

            return (new OKResponse("OK", $departmentList, $newToken))->GetResponse();
        });
    }

    /*
     * DEPARTMENT
     * Crea un departamento
     * Funcion: 'vaa.operar'
     */
    public function crear($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST insert department", basename(__FILE__), "insert");

            $departmentDTO = new DepartmentDTO($request->getParsedBody());
            $idEmpresa = $departmentDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";
            
            $departmentSrv = new DepartmentService();
            $countDepartamentos = $departmentSrv->getCountDepartments($departmentDTO->getBusinessId());
            $empresaSrv = new EmpresaService();
            $empresaDTO = $empresaSrv->getEmpresa($departmentDTO->getBusinessId());
            if ($countDepartamentos + 1 > $empresaDTO->getCant_max_departamentos()) {
                return (new ErrorResponse("Excede el límite máximo de " . $empresaDTO->getCant_max_departamentos() . " departamentos. Contactarse con EDS."))->GetResponse();
            }
            $id = $departmentSrv->guardarDepartment($departmentDTO);
            $itemCreado = new ItemCreadoDTO($id);
            $itemCreadoList = ControllerUtils::AsArrayList($itemCreado);

            return (new OKResponse("OK", $itemCreadoList, $newToken))->GetResponse();
        });
    }

    /*
     * DEPARTMENT
     * Modifica un departamento
     * Funcion: 'vaa.operar'
     */
    public function modificar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST update departamento", basename(__FILE__), "update");

            $departmentDTO = new DepartmentDTO($request->getParsedBody());
            $idEmpresa = $departmentDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $departmentSrv->guardarDepartment($departmentDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * DEPARTMENT
     * Elimina un departamento
     * Funcion: 'vaa.operar'
     */
    public function eliminar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST eliminar departamento", basename(__FILE__), "update");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $departmentSrv->eliminarDepartment($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * CANTIDAD DE DEPARTMENTS
     * Obtiene el total de departments de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getCantidadDepartmentPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener cantidad departments", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $departmentSrv = new DepartmentService();
            $countDepartment = $departmentSrv->getCountDepartments($request->getAttribute('idEmpresa'));

            return (new OKResponse("OK", $countDepartment, $newToken))->GetResponse();
        });
    }

}