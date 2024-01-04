<?php

namespace model;

use dto\FeriadoDTO;
use \utils\CommonUtils;;

class Feriado {

    private $hol_date;
    private $hol_date_type;
    private $hol_description;
    private $business_id;
	private $COMPANY_ID;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setHolDate(CommonUtils::GetArrayValue('HOL_DATE', $data));
            $this->setHolDateType(CommonUtils::GetArrayValue('HOL_DAY_TYPE', $data));
            $this->setHolDescription(CommonUtils::GetArrayValue('HOL_DESCRIPTION', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        } else if ($data instanceof FeriadoDTO) {
            $this->setHolDate($data->getHolDate());
            $this->setHolDateType($data->getHolDateType());
            $this->setHolDescription($data->getHolDescription());
            $this->setBusinessId($data->getBusinessId());
        }
    }

    /**
     * @return mixed
     */
    public function getHolDate()
    {
        return $this->hol_date;
    }

    /**
     * @param mixed $hol_date
     */
    public function setHolDate($hol_date)
    {
        $this->hol_date = $hol_date;
    }

    /**
     * @return mixed
     */
    public function getHolDateType()
    {
        return $this->hol_date_type;
    }

    /**
     * @param mixed $hol_date_type
     */
    public function setHolDateType($hol_date_type)
    {
        $this->hol_date_type = $hol_date_type;
    }

    /**
     * @return mixed
     */
    public function getHolDescription()
    {
        return $this->hol_description;
    }

    /**
     * @param mixed $hol_description
     */
    public function setHolDescription($hol_description)
    {
        $this->hol_description = $hol_description;
    }

    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->COMPANY_ID;
    }

    /**
     * @param mixed $business_id
     */
    public function setBusinessId($business_id)
    {
        $this->COMPANY_ID = $business_id;
    }

}
