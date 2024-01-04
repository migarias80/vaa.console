<?php

namespace exception;


class EmpresaRepetidaException extends \Exception
{

    private $customCode = ITEM_REPEDITO;

    public function __construct($urlEmpresa) {
        parent::__construct("La URL de la empresa " . $urlEmpresa . " ya se encuentra registrada en el sistema", $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}