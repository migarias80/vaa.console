<?php

namespace dto;

use model\QueryValidation;
use \utils\CommonUtils;

class QueryValidationDTO implements IGenericDTO
{

    private $value;
    private $desc;

    function __construct(QueryValidation $queryValidation = null)
    {
        if ($queryValidation == null) {
            return;
        }
        $this->setValue($queryValidation->getValue());
        $this->setDesc($queryValidation->getDesc());
    }

    public function constructFromArray($data)
    {
        $this->setValue(CommonUtils::GetArrayValue('VALUE', $data));
        $this->setDesc(CommonUtils::GetArrayValue('DESC', $data));
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

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

}