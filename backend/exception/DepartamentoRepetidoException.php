<?php

namespace exception;

class DepartamentoRepetidoException extends \Exception
{

    private $customCode = ITEM_REPEDITO;

    public function __construct($depName) {
        parent::__construct("El departamento " . $depName . " ya se encuentra registrado en el sistema", $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}