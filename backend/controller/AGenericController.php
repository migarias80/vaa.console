<?php

namespace controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \exception\MandatoryRequestException;
use \exception\UsuarioSinPermisosException;
use \exception\InvalidTokenException;
use \Psr\Http\Message\ResponseInterface as Response;
use \responses\ForbidenReponse;
use \responses\ErrorResponse;
use \responses\ErrorRequest;

class AGenericController
{

    public function excecute(Request $request, Response $response, $callable) {
        try {
            return call_user_func_array($callable, array($request, $response));
        }catch (UsuarioSinPermisosException $e) {
            return (new ForbidenReponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (InvalidTokenException $e) {
            return (new ErrorResponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (MandatoryRequestException $e) {
            return (new ErrorResponse($e->getMessage(), $e->getCustomCode()))->GetResponse();
        }catch (PDOException $e) {
            return (new ErrorResponse("Ocurrio un error al conectarse con la base de datos", CODE_ERROR_INESPERADO, $e))->GetResponse();
        }catch (Exception $e) {
            return (new ErrorResponse("Ocurrio un error inesperado al realizar la accion", CODE_ERROR_INESPERADO, $e))->GetResponse();
        }
    }

}