<?php

namespace dto;

use model\OperationMode;
use utils\CommonUtils;

class OperationModeDTO implements IGenericDTO
{

    private $opm_code;
    private $opm_description;

    function __construct(OperationMode $operationMode = null)
    {
        if ($operationMode == null) {
            return;
        }
        $this->setOpmCode($operationMode->getOpmCode());
        $this->setOpmDescription($operationMode->getOpmDescription());
    }

    function constructFromArray($data)
    {
        $this->setOpmCode(CommonUtils::GetArrayValue($data, 'OPM_CODE'));
        $this->setOpmDescription(CommonUtils::GetArrayValue($data, 'OPM_DESCRIPTION'));
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
    public function getOpmCode()
    {
        return $this->opm_code;
    }

    /**
     * @param mixed $opm_code
     */
    public function setOpmCode($opm_code)
    {
        $this->opm_code = $opm_code;
    }

    /**
     * @return mixed
     */
    public function getOpmDescription()
    {
        return $this->opm_description;
    }

    /**
     * @param mixed $opm_description
     */
    public function setOpmDescription($opm_description)
    {
        $this->opm_description = $opm_description;
    }


}