<?php

namespace exception;

class UsuarioRepetidoException extends \Exception
{

    private $customCode = CODE_USUARIO_YA_EXISTE;

    public function __construct($nombreUsuario) {
        parent::__construct("El usuario " . $nombreUsuario . " ya se encuentra registrado en el sistema", $this->getCustomCode(), null);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getCustomCode() {
        return $this->customCode;
    }

}