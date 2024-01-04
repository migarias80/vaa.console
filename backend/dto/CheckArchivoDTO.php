<?php

namespace dto;

use \utils\CommonUtils;
use utils\ControllerUtils;

class CheckArchivoDTO implements IGenericDTO {

    private $archivo;
    private $tipo; // 1: Depto, 2: Persona, 3: Secretaria

    function __construct() {

    }

    public function constructFromArray($data) {
        $this->setArchivo(CommonUtils::GetArrayValue('ARCHIVO', $data));
        $this->setTipo(CommonUtils::GetArrayValue('TIPO', $data));
    }

    public function toArray() {
        $returnArray = [];
        foreach ($this as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $itemInArray) {
                    if (is_object($itemInArray)) {
                        $returnArray[$key][] = $itemInArray->toArray();
                    } else {
                        $returnArray[$key][] = $itemInArray;
                    }
                }
            } else {
                $returnArray[$key] = $value;
            }
        }
        return $returnArray;
    }

    public function getArchivo() {
        return $this->archivo;
    }

    public function setArchivo($archivo) {
        $this->archivo = $archivo;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

}
