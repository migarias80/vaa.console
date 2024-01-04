<?php

namespace model;

use dto\BandaHorariaDTO;
use \utils\CommonUtils;

class BandaHoraria
{
    private $ban_day_type;
    private $ban_start_hour;
    private $ban_opm_code;
    private $ban_end_hour;
    private $ban_description;
    private $business_id;
	private $COMPANY_ID;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setBanDayType(CommonUtils::GetArrayValue('BAN_DAY_TYPE', $data));
            $this->setBanStartHour(CommonUtils::GetArrayValue('BAN_START_HOUR', $data));
            $this->setBanOpmCode(CommonUtils::GetArrayValue('BAN_OPM_CODE', $data));
            $this->setBanEndHour(CommonUtils::GetArrayValue('BAN_END_HOUR', $data));
            $this->setBanDescription(CommonUtils::GetArrayValue('BAN_DESCRIPTION', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        } else if ($data instanceof BandaHorariaDTO) {
            $this->setBanDayType($data->getBanDayType());
            $this->setBanStartHour($data->getBanStartHour());
            $this->setBanOpmCode($data->getBanOpmCode());
            $this->setBanEndHour($data->getBanEndHour());
            $this->setBanDescription($data->getBanDescription());
            $this->setBusinessId($data->getBusinessId());
        }
    }

    /**
     * @return mixed
     */
    public function getBanDayType()
    {
        return $this->ban_day_type;
    }

    /**
     * @param mixed $ban_day_type
     */
    public function setBanDayType($ban_day_type)
    {
        $this->ban_day_type = $ban_day_type;
    }

    /**
     * @return mixed
     */
    public function getBanStartHour()
    {
        return $this->ban_start_hour;
    }

    /**
     * @param mixed $ban_start_hour
     */
    public function setBanStartHour($ban_start_hour)
    {
        $this->ban_start_hour = $ban_start_hour;
    }

    /**
     * @return mixed
     */
    public function getBanOpmCode()
    {
        return $this->ban_opm_code;
    }

    /**
     * @param mixed $ban_opm_code
     */
    public function setBanOpmCode($ban_opm_code)
    {
        $this->ban_opm_code = $ban_opm_code;
    }

    /**
     * @return mixed
     */
    public function getBanEndHour()
    {
        return $this->ban_end_hour;
    }

    /**
     * @param mixed $ban_end_hour
     */
    public function setBanEndHour($ban_end_hour)
    {
        $this->ban_end_hour = $ban_end_hour;
    }

    /**
     * @return mixed
     */
    public function getBanDescription()
    {
        return $this->ban_description;
    }

    /**
     * @param mixed $ban_description
     */
    public function setBanDescription($ban_description)
    {
        $this->ban_description = $ban_description;
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

