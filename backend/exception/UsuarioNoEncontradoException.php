<?php

namespace exception;

class UsuarioNoEncontradoException extends \Exception
{

    private $customCode = CODE_USUARIO_SIN_PERMISOS;

    public function __construct() {
        parent::__construct("El Usuario no encontrado", $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}