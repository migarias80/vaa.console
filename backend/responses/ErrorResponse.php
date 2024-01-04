<?php

namespace responses;

class ErrorResponse extends GenericResponse
{

    function __construct($message, $code=CODE_ERROR_INESPERADO, $exception=null)
    {
        parent::__construct(500);
        parent::SetCode($code);
        
        if ($exception == null) {
            parent::SetMessage($message);
        } else {
            // parent::SetMessage($message . " [" . $exception->getMessage() . " - " . $exception->getFile() .": " . $exception->getLine() . "]");
            parent::SetMessage($exception->getMessage());
        }
    }

}