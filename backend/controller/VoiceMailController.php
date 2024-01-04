<?php

use \utils\SecurityUtils;
use \utils\ControllerUtils;
use \responses\OkResponse;
use \responses\ErrorRequest;
use \exception\MandatoryRequestException;
use \serviceimpl\VoiceMailService;
use \utils\LogUtils;
use \dto\VoiceMailDTO;
use \dto\ItemCreadoDTO;

/**
 * VOICE MAIL API REST
 */
$app->group('/voicemail/', function () use ($app) {

	// PHP 7
	// $p = "";

	// PHP 8
	$p = "/voicemail/";

    $app->get($p.'{idEmpresa}', VoiceMailController::class . ':getVoiceMailPorEmpresa');
    $app->get($p.'{idEmpresa}/{id}', VoiceMailController::class . ':getVoiceMailPorId');
    $app->post($p.'crear', VoiceMailController::class . ':crear');
    $app->post($p.'modificar', VoiceMailController::class . ':modificar');
    $app->post($p.'eliminar/{idEmpresa}/{id}', VoiceMailController::class . ':eliminar');

});

/**
 * Definicion de clase
 */
class VoiceMailController extends \controller\AGenericController
{
    /*
     * VOICE-MAIL
     * Obtiene todos los voice mails de una empresa
     * Funcion: 'vaa.operar'
     */
    public function getVoiceMailPorEmpresa($request, $response, $args)
    {
        return $this->excecute($request, $response, function ($request, $response) {
            LogUtils::INFO("REST obtener voice mails", basename(__FILE__), "get");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute("idEmpresa"));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $voiceMailSrv = new VoiceMailService();
            $voiceMail = $voiceMailSrv->getVoiceMail($request->getAttribute('idEmpresa'));
            $voiceMailList = ControllerUtils::AsArrayList($voiceMail);

            return (new OKResponse("OK", $voiceMailList, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Obtiene un voice mail en base a empresa y id
     * Funcion: 'vaa.operar'
     */
    public function getVoiceMailPorId($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response){
            LogUtils::INFO("REST obtener voice mail", basename(__FILE__), "load");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $voiceMailSrv = new VoiceMailService();
            $voiceMail = $voiceMailSrv->getVoiceMail($request->getAttribute('idEmpresa'), $request->getAttribute('id'));
            $voiceMailList = ControllerUtils::AsArrayList($voiceMail);

            return (new OKResponse("OK", $voiceMailList, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Crea un voice mail
     * Funcion: 'vaa.operar'
     */
    public function crear($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST insert voice mail", basename(__FILE__), "insert");

            $voiceMailDTO = new VoiceMailDTO();
            $voiceMailDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $voiceMailDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $voiceMailSrv = new VoiceMailService();
            $id =  $voiceMailSrv->guardar($voiceMailDTO);
            $itemCreado = new ItemCreadoDTO($id);
            $itemCreadoList = ControllerUtils::AsArrayList($itemCreado);

            return (new OKResponse("OK", $itemCreadoList, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Modifica un voice mail
     * Funcion: 'vaa.operar'
     */
    public function modificar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST update voice mail", basename(__FILE__), "update");

            $voiceMailDTO = new VoiceMailDTO();
            $voiceMailDTO->constructFromArray($request->getParsedBody());
            $idEmpresa = $voiceMailDTO->getBusiness_id();
            if ($idEmpresa == null) {
                throw new MandatoryRequestException();
            }

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $idEmpresa);
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $voiceMailSrv = new VoiceMailService();
            $voiceMailSrv->guardar($voiceMailDTO);

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

    /*
     * VOICE-MAIL
     * Elimina un voice mail
     * Funcion: 'vaa.operar'
     */
    public function eliminar($request, $response, $args) {
        return ControllerUtils::ExecuteController($request, $response, function($request, $response) {
            LogUtils::INFO("REST eliminar voice mail", basename(__FILE__), "eliminar");

            ControllerUtils::VerifyMandatoryRequest($request, array("idEmpresa", "id"));

            $decodedToken = SecurityUtils::CheckPermissions(
                $request->getHeader("Authorization"),
                "vaa.operar",
                $request->getAttribute('idEmpresa'));
            // $newToken = SecurityUtils::GetToken($decodedToken->uid);
            $newToken = "";

            $voiceMailSrv = new VoiceMailService();
            $voiceMailSrv->eliminar($request->getAttribute('idEmpresa'), $request->getAttribute('id'));

            return (new OKResponse("OK", null, $newToken))->GetResponse();
        });
    }

}