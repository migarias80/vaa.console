<?php

namespace utils;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \exception\MandatoryRequestException;
use \exception\UsuarioSinPermisosException;
use \exception\InvalidTokenException;
use \Psr\Http\Message\ResponseInterface as Response;
use \responses\ForbidenReponse;
use \responses\ErrorResponse;
use \responses\ErrorRequest;
use \utils\LogUtils;

abstract class ControllerUtils
{

    static function AsArrayList($arrayDTO) {
        $array = [];
        if (is_array($arrayDTO)) {
            foreach ($arrayDTO as $dto) {
                $array[] = $dto->toArray();
            }
            return $array;
        } else if ($arrayDTO == null) {
            return $array;
        } else {
            return $arrayDTO->toArray();
        }
    }

    static function VerifyMandatoryRequest(Request $request, $arrayMandatory) {
        foreach ($arrayMandatory as $madatoryItem) {
            if ($request->getAttribute($madatoryItem) == 'null' || $request->getAttribute($madatoryItem) == null) {
                throw new MandatoryRequestException;
            }
        }
    }

    static function ExecuteController(Request $request, Response $response, $callable) {
        try {
            return call_user_func_array($callable, array($request, $response));
        }catch (UsuarioSinPermisosException $e) {
            LogUtils::ERROR($e->getMessage(), basename(__FILE__), "UsuarioSinPermisosException");
            return (new ForbidenReponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (InvalidTokenException $e) {
            LogUtils::ERROR($e->getMessage(), basename(__FILE__), "InvalidTokenException");
            return (new ErrorResponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (MandatoryRequestException $e) {
            LogUtils::ERROR($e->getMessage(), basename(__FILE__), "MandatoryRequestException");
            return (new ErrorResponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (PDOException $e) {
            LogUtils::ERROR($e->getMessage(), basename(__FILE__), "PDOException");
            return (new ErrorResponse("Ocurrio un error al conectarse con la base de datos", CODE_ERROR_INESPERADO, $e))->GetResponse();
        }catch (Exception $e) {
            LogUtils::ERROR($e->getMessage(), basename(__FILE__), "Exception");
            return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO, $e))->GetResponse();
        }
    }

    static function JSONToDTO() {

    }

    static function DTOToJSON() {

    }

}