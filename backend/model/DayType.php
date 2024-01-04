<?php

namespace model;

use \utils\CommonUtils;

class DayType
{

    private $dat_day_type;
    private $dat_description;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setDatDayType(CommonUtils::GetArrayValue('DAT_DAY_TYPE', $data));
        $this->setDatDescription(CommonUtils::GetArrayValue('DAT_DESCRIPTION', $data));
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