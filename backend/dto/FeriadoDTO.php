<?php

namespace dto;

use model\Feriado;
use \utils\CommonUtils;

class FeriadoDTO implements IGenericDTO
{

    private $hol_date;
    private $hol_date_type;
    private $hol_description;
    private $business_id;

    private $hol_date_old;
	private $COMPANY_ID;

    function __construct($data = null) {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setHolDate(CommonUtils::GetArrayValue('HOL_DATE', $data));
            $this->setHolDateType(CommonUtils::GetArrayValue('HOL_DAY_TYPE', $data));
            $this->setHolDescription(CommonUtils::GetArrayValue('HOL_DESCRIPTION', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
            $this->setHolDateOld(CommonUtils::GetArrayValue('HOL_DATE_OLD', $data));
        } else if ($data instanceof Feriado) {
            $this->setHolDate($data->getHolDate());
            $this->setHolDateType($data->getHolDateType());
            $this->setHolDescription($data->getHolDescription());
            $this->setBusinessId($data->getBusinessId());
        }
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

    /**
     * @return mixed
     */
    public function getHolDateOld()
    {
        return $this->hol_date_old;
    }

    /**
     * @param mixed $hol_date_old
     */
    public function setHolDateOld($hol_date_old)
    {
        $this->hol_date_old = $hol_date_old;
    }

}