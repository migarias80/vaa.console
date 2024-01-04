<?php

namespace dto;

use model\DayType;
use utils\CommonUtils;

class DayTypeDTO implements IGenericDTO
{

    private $dat_day_type;
    private $dat_description;

    function __construct(DayType $dayType = null)
    {
        if ($dayType == null) {
            return;
        }
        $this->setDatDayType($dayType->getDatDayType());
        $this->setDatDescription($dayType->getDatDescription());
    }

    function constructFromArray($data)
    {
        $this->setDatDayType(CommonUtils::GetArrayValue($data, 'DAT_DAY_TYPE'));
        $this->setDatDescription(CommonUtils::GetArrayValue($data, 'DAT_DESCRIPTION'));
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
    public function getDatDayType()
    {
        return $this->dat_day_type;
    }

    /**
     * @param mixed $dat_day_type
     */
    public function setDatDayType($dat_day_type)
    {
        $this->dat_day_type = $dat_day_type;
    }

    /**
     * @return mixed
     */
    public function getDatDescription()
    {
        return $this->dat_description;
    }

    /**
     * @param mixed $dat_description
     */
    public function setDatDescription($dat_description)
    {
        $this->dat_description = $dat_description;
    }

}