<?php

use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \utils\LogUtils;
use \serviceimpl\ParametroService;
use \utils\SecurityUtils;
use \exception\MandatoryRequestException;
use \dto\ParametroDTO;
use \dto\FeriadoDTO;
use \exception\UsuarioSinPermisosException;

/**
 * PARAMETRO API REST
 */
$app->group('/parametros/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/parametros/";

    // OPCIONES VARIAS
    $app->get($p.'confirm', ParametroController::class . ':getOpcionesDeConfirmacion');
    $app->get($p.'trans', ParametroController::class . ':getOpcionesDeTransferencia');
    $app->get($p.'grammar-opt', ParametroController::class . ':getOpcionesGramaticas');
    $app->get($p.'day-types', ParametroController::class . ':getDayTypes');
    $app->get($p.'operation-modes', ParametroController::class . ':getOperationModes');

    // BANDAS HORARIAS
    $app->get($p.'bandas-horarias/{idEmpresa}', ParametroController::class . ':getBandasHorarias');
    $app->post($p.'bandas-horarias/crear', ParametroController::class . ':crearBandasHorarias');
    $app->post($p.'bandas-horarias/modificar', ParametroController::class . ':modificarBandaHoraria');
    $app->post($p.'bandas-horarias/eliminar/{idEmpresa}/{dayType}/{opmCode}/{start}/{end}', ParametroController::class . ':eliminarBandaHoraria');
    $app->post($p.'bandas-horarias/eliminar/{idEmpresa}/{dayType}', ParametroController::class . ':eliminarBandaHorariaPorDia');

    // PARAMETROS
    $app->get($p.'get/{idEmpresa}', ParametroController::class . ':getParametros');
    $app->get($p.'query-validation/{parName}', ParametroController::class . ':getQueryValidation');
    $app->get($p.'query-validation-queryid/{queryId}/{idEmpresa}', ParametroController::class . ':getQueryValidationByQueryId');
    $app->post($p.'modificar', ParametroController::class . ':modificarParametros');
    $app->post($p.'get/{idEmpresa}', ParametroController::class . ':getParametrosFiltrados');

    // FERIADOS
    $app->get($p.'feriados/{idEmpresa}', ParametroController::class . ':getFeriados');
    $app->post($p.'feriados/crear', ParametroController::class . ':crearFeriado');
    $app->post($p.'feriados/modificar', ParametroController::class . ':modificarFeriado');
    $app->post($p.'feriados/eliminar/{idEmpresa}/{id}', ParametroController::class . ':eliminarFeriado');

    // MODO DE OPERACION DE LA CONSOLA
    $app->get($p.'modo-operacion-consola', ParametroController::class . ':getModoDeOperacionDeLaConsola');
});

/**
 * Definicion de clase
 */
class ParametroController extends \controller\AGenericController
{

    /*
     * PARAMETRO
     * Obtiene las opciones de confirmacion
     * Funcion: 'PERMIT-ALL'
     */
    public function getOpcionesDeConfirmacion($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener opciones de confirmacion", basename(__FILE__), "getOpcionesDeConfirmacion");

            $newToken = $request->getHeader("Authorization");

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getConfirmationOptions();
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene las acciones de transferencia
     * Funcion: 'PERMIT-ALL'
     */
    public function getOpcionesDeTransferencia($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener opciones de transferencia", basename(__FILE__), "getOpcionesDeTransferencia");

            $newToken = $request->getHeader("Authorization");

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getTransferOptions();
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene las acciones de gramatica
     * Funcion: 'PERMIT-ALL'
     */
    public function getOpcionesGramaticas($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener opciones de gramatica", basename(__FILE__), "getOpcionesGramaticas");

            $newToken = $request->getHeader("Authorization");

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getOpcionesGramaticas();
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene los tipos de dia
     * Funcion: 'PERMIT-ALL'
     */
    public function getDayTypes($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener los tipos de dia", basename(__FILE__), "getDayTypes");

            $newToken = $request->getHeader("Authorization");

            $parametroSrv = new ParametroService();
            $dayTypes = $parametroSrv->getDayTypes();
            $dayTypesList = ControllerUtils::AsArrayList($dayTypes);

            return (new OKResponse("OK", $dayTypesList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene los modo de operacion
     * Funcion: 'PERMIT-ALL'
     */
    public function getOperationModes($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener los modo de operacion", basename(__FILE__), "getOperationModes");

            $newToken = $request->getHeader("Authorization");

            $parametroSrv = new ParametroService();
            $operationModes = $parametroSrv->getOperationModes();
            $operationModesList = ControllerUtils::AsArrayList($operationModes);

            return (new OKResponse("OK", $operationModesList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene las bandas horarias de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getBandasHorarias($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener bandas horarias", basename(__FILE__), "getBandasHorarias");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getBandasHorarias($request->getAttribute('idEmpresa'));
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Crea un listado de bandas horarias de una empresa
     * Funcion: 'vaa.operar'
     */
    public function crearBandasHorarias($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST crear bandas horarias", basename(__FILE__), "crearBandasHorarias");

            $bandasDTO = [];
            foreach ($request->getParsedBody() as $banda) {
                $bandaHorariaDTO = new \dto\BandaHorariaDTO();
                $bandaHorariaDTO->constructFromArray($banda);
                $bandasDTO[] = $bandaHorariaDTO;
                $idEmpresa = $bandaHorariaDTO->getBusinessId();
                $dayType = $bandaHorariaDTO->getBanDayType();
                if ($idEmpresa == null) {
                    throw new MandatoryRequestException();
                }
                if ($dayType == null) {
                    throw new MandatoryRequestException();
                }
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->eliminarBandaHorariaPorDia($idEmpresa, $dayType);
            foreach ($bandasDTO as $bandaDTO) {
                $options = $parametroSrv->crearBandaHoraria($bandaDTO);
            }

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Modifica una banda horaria de una empresa
     * Funcion: 'vaa.operar'
     */
    public function modificarBandaHoraria($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST modificar banda horaria", basename(__FILE__), "modificarBandaHoraria");

            $bandaHorariaDTO = new \dto\BandaHorariaDTO();
            $bandaHorariaDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $bandaHorariaDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->modificarBandaHoraria($bandaHorariaDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Elimina una banda horaria de una empresa
     * Funcion: 'vaa.operar'
     */
    public function eliminarBandaHoraria($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar banda horaria", basename(__FILE__), "eliminarBandaHoraria");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "dayType", "opmCode", "start", "end"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->eliminarBandaHoraria(
                $request->getAttribute('idEmpresa'), 
                $request->getAttribute('dayType'), 
                $request->getAttribute('opmCode'),
                $request->getAttribute('start'),
                $request->getAttribute('end')
            );

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Elimina las bandas horarias correspondientes a un determinado dia de una empresa
     * Funcion: 'vaa.operar'
     */
    public function eliminarBandaHorariaPorDia($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST eliminar bandas horarias del dia", basename(__FILE__), "eliminarBandaHorariaPorDia");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "dayType"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->eliminarBandaHorariaPorDia($request->getAttribute('idEmpresa'), $request->getAttribute('dayType'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene los parametros de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getParametros($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener parametros", basename(__FILE__), "getParametros");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getParametros($request->getAttribute('idEmpresa'));
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
    * PARAMETROS
    * Obtiene los parametros de una empresa (filtrados por par_name)
    * Funcion: 'vaa.operar'
    */
    public function getParametrosFiltrados($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener parametros filtrados", basename(__FILE__), "getParametros");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametrosFilter = $request->getParsedBody()['parametros'];

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getParametros($request->getAttribute('idEmpresa'), $parametrosFilter);
            $optionsList = ControllerUtils::AsArrayList($options);

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
    * PARAMETROS
    * Obtiene el query validation de un parametro
    * Funcion: 'vaa.operar'
    */
    public function getQueryValidation($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener query validation", basename(__FILE__), "INICIO " . $request->getAttribute('parName'));

            ControllerUtils::VerifyMandatoryRequest($request, array("parName"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $id_empresa = $decodedToken->bid;

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getQueryValidation($request->getAttribute('parName'), $id_empresa);
            $optionsList = ControllerUtils::AsArrayList($options);

            LogUtils::INFO("REST obtener query validation", basename(__FILE__), "FIN " . $request->getAttribute('parName'));

            return (new OKResponse("OK", $optionsList, $newToken))->GetResponse();
        });
    }

    /*
    * PARAMETROS
    * Obtiene el query validation de un parametro por el query_id
    * Funcion: 'vaa.operar'
    */
    public function getQueryValidationByQueryId($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener query validation", basename(__FILE__), "INICIO " . $request->getAttribute('parName'));

            ControllerUtils::VerifyMandatoryRequest($request, array("queryId"));
            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar");
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            if ($decodedToken->bid == 1) {
                $id_empresa = $request->getAttribute('idEmpresa');
            } else {
                $id_empresa = $decodedToken->bid;
            }

            $parametroSrv = new ParametroService();
            $options = $parametroSrv->getQueryValidationByQueryId($request->getAttribute('queryId'), $id_empresa);
            $optionsList = ControllerUtils::AsArrayList($options);
            $data = array();
            $data['options'] = $optionsList;
            $data['query_id'] = $request->getAttribute('queryId');

            return (new OKResponse("OK", $data, $newToken))->GetResponse();
        });
    }

    /*
    * PARAMETROS
    * Modifica los parametros de una empresa
    * Funcion: 'vaa.operar'
    */
    public function modificarParametros($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST modificar parametros", basename(__FILE__), "modificarParametros");

            $parametroDTO = new ParametroDTO();
            $parametroSrv = new ParametroService();

            $items = $request->getParsedBody();
            $items = $items['parametros'];
            foreach ($items as $item) {
                $parametroDTO = new ParametroDTO();
                $parametroDTO->constructFromArray($item);

                $decodedToken = SecurityUtils::CheckPermissions(
                    $request->getHeader("Authorization"),
                    "vaa.operar",
                    $parametroDTO->getBusinessId());
                // $newToken = SecurityUtils::GetToken($decodedToken->uid);
                $newToken = "";
                
                // Si son parametros de configuracion solo SA de EDS puede editarlos
                // Siempre y cuando se trate de un modo de consola MULTI
                /* if ($parametroDTO->getParOrder() == -1) {
                    if ($decodedToken->bid != 1){
                        throw new UsuarioSinPermisosException();
                    }
                } */

                $parametroSrv->modificarParametro($parametroDTO);
            }

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene los feriados de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getFeriados($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener feriados", basename(__FILE__), "getFeriados");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $feriados = $parametroSrv->getFeriados($request->getAttribute('idEmpresa'));
            $feriadosList = ControllerUtils::AsArrayList($feriados);

            return (new OKResponse("OK", $feriadosList, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Crea un feriado
     * Funcion: 'vaa.operar'
     */
    public function crearFeriado($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST insert feriado", basename(__FILE__), "crearFeriado");

            $feriadoDTO = new FeriadoDTO($request->getParsedBody());
            $idEmpresa = $feriadoDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

           $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->crearFeriado($feriadoDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Modifica un feriado
     * Funcion: 'vaa.operar'
     */
    public function modificarFeriado($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST modificar feriado", basename(__FILE__), "crearFeriado");

            $feriadoDTO = new FeriadoDTO($request->getParsedBody());
            $idEmpresa = $feriadoDTO->getBusinessId();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->modificarFeriado($feriadoDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Elimina un feriado
     * Funcion: 'vaa.operar'
     */
    public function eliminarFeriado($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST eliminar feriado", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $parametroSrv = new ParametroService();
            $parametroSrv->eliminarFeriado($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * PARAMETROS
     * Obtiene el modo de operacion de una consola MONO o MULTI (Siempre MULTI)
     * Funcion: 'vaa.operar'
     */
    public function getModoDeOperacionDeLaConsola($request, $response, $args) {
        return $this->excecute($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener modo de operacion de la consola", basename(__FILE__), "getFeriados");

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $modoDeOperacionConsola = "MULTI";

            return (new OKResponse("OK", $modoDeOperacionConsola, $newToken))->GetResponse();
        });
    }

}