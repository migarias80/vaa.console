<?php

namespace dto;

use model\Empresa;
use \utils\CommonUtils;

class EmpresaEvalDTO implements IGenericDTO
{

    private $id;
    private $departamentos_estado;
    private $personas_estado;

    function __construct()
    {

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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDepartamentosEstado()
    {
        return $this->departamentos_estado;
    }

    /**
     * @param mixed $departamentos_estado
     */
    public function setDepartamentosEstado($departamentos_estado)
    {
        $this->departamentos_estado = $departamentos_estado;
    }

    /**
     * @return mixed
     */
    public function getPersonasEstado()
    {
        return $this->personas_estado;
    }

    /**
     * @param mixed $personas_estado
     */
    public function setPersonasEstado($personas_estado)
    {
        $this->personas_estado = $personas_estado;
    }

}