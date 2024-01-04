<?php

namespace dto;

use \utils\CommonUtils;
use utils\ControllerUtils;

class ItemCreadoDTO implements IGenericDTO {

    private $id;

    function __construct($id) {
        $this->setId($id);
    }

    public function constructFromArray($data) {
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
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

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

}
