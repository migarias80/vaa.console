<?php

namespace exception;

class InvalidTokenException extends \Exception
{

    private $customCode = CODE_TOKEN_INVALIDO;

    public function __construct($message="El Token es incorrecto") {
        parent::__construct($message, $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}