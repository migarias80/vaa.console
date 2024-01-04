<?php

namespace exception;


class MandatoryRequestException extends \Exception
{

    private $customCode = CODE_ERROR_FALTAN_PARAMETROS;

    public function __construct() {
        parent::__construct("Parámetros incorrectos", $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}