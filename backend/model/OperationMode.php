<?php

namespace model;

use \utils\CommonUtils;

class OperationMode
{

    private $opm_code;
    private $opm_description;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setOpmCode(CommonUtils::GetArrayValue('OPM_CODE', $data));
        $this->setOpmDescription(CommonUtils::GetArrayValue('OPM_DESCRIPTION', $data));
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