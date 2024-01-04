<?php

namespace dto;

use \model\ConfirmationOption;
use \utils\CommonUtils;

class ConfirmationOptionDTO implements IGenericDTO
{

    private $oco_id;
    private $oco_description;

    function __construct(ConfirmationOption $option = null)
    {
        if ($option == null) { return; }
        $this->setOco_id($option->getOco_id());
        $this->setOco_description($option->getOco_description());
    }

    public function constructFromArray($data)
    {
        $this->setOco_id(CommonUtils::GetArrayValue('OCO_ID', $data));
        $this->setOco_description(CommonUtils::GetArrayValue('OCO_DESCRIPTION', $data));
    }

    public function toArray()
    {
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

    public function getOco_id() {
        return $this->oco_id;
    }

    public function setOco_id($oco_id) {
        $this->oco_id = $oco_id;
    }


    public function getOco_description() {
        return $this->oco_description;
    }

    public function setOco_description($oco_description) {
        $this->oco_description = $oco_description;
    }
}