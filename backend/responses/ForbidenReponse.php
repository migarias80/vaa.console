<?php

namespace responses;

class ForbidenReponse extends GenericResponse
{

    function __construct($message, $code=CODE_ERROR_INESPERADO)
    {
        parent::__construct(200);
        parent::SetMessage($message);
        parent::SetCode($code);
    }

}