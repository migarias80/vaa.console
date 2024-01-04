<?php

namespace dto;

use \model\BandaHoraria;
use \utils\CommonUtils;

class BandaHorariaDTO implements IGenericDTO
{

    private $ban_day_type;
    private $ban_start_hour;
    private $ban_opm_code;
    private $ban_end_hour;
    private $ban_description;
    private $business_id;

    private $ban_day_type_old;
    private $ban_start_hour_old;
    private $ban_start_hour_value;
    private $ban_end_hour_value;
    private $ban_complement;
	private $COMPANY_ID;

    function __construct(BandaHoraria $bandaHoraria = null)
    {
        if ($bandaHoraria == null) { return; }

        // TODO: Obsoleto
        // El BAN_END_HOUR se guarda como xx59 incialmente,
        // Es por eso que se ajusta dicho valor.
        // En el caso de ser 2359 (por ej) pasa a 2400, de esta forma 0000 - 2400 da un total de 24hs
        // Si fuera 0759, entonces sería 0000 - 0800 = 8hs
        /* if (substr($bandaHoraria->getBanEndHour(), 2) != "0") {
            $auxEnd = substr($bandaHoraria->getBanEndHour(), 0, 2) + 1;
            $auxEnd = str_pad($auxEnd, 2, "0", STR_PAD_LEFT);
            $auxEnd = $auxEnd . "00";
            $this->setBanEndHour($auxEnd);
        } else {
            $this->setBanEndHour($bandaHoraria->getBanEndHour());
        } */

        $this->setBanEndHour($bandaHoraria->getBanEndHour());

        $this->setBanDayType($bandaHoraria->getBanDayType());
        $this->setBanStartHour($bandaHoraria->getBanStartHour());
        $this->setBanOpmCode($bandaHoraria->getBanOpmCode());
        $this->setBanDescription($bandaHoraria->getBanDescription());
        $this->setBusinessId($bandaHoraria->getBusinessId());

        // Una vez que el DTO se encuentra cargado completamente
        // Tomo el valor entero de la hora, por ejemplo 0800 es 8
        $auxStart = substr($this->getBanStartHour(), 0, 2);
        $this->setBanStartHourValue(intval($auxStart));
        $auxEnd = substr($this->getBanEndHour(), 0, 2);
        $this->setBanEndHourValue(intval($auxEnd));
    }

    public function constructFromArray($data)
    {
        $this->setBanDayType(CommonUtils::GetArrayValue('BAN_DAY_TYPE', $data));
        $this->setBanStartHour(CommonUtils::GetArrayValue('BAN_START_HOUR', $data));
        $this->setBanOpmCode(CommonUtils::GetArrayValue('BAN_OPM_CODE', $data));
        $this->setBanEndHour(CommonUtils::GetArrayValue('BAN_END_HOUR', $data));
        $this->setBanDescription(CommonUtils::GetArrayValue('BAN_DESCRIPTION', $data));
        $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        $this->setBanDayTypeOld(CommonUtils::GetArrayValue('BAN_DAY_TYPE_OLD', $data));
        $this->setBanStartHourOld(CommonUtils::GetArrayValue('BAN_START_HOUR_OLD', $data));
        $this->setBanComplement(CommonUtils::GetArrayValue('BAN_COMPLEMENT', $data));
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

    /**
     * @return mixed
     */
    public function getBanDayTypeOld()
    {
        return $this->ban_day_type_old;
    }

    /**
     * @param mixed $ban_day_type_old
     */
    public function setBanDayTypeOld($ban_day_type_old)
    {
        $this->ban_day_type_old = $ban_day_type_old;
    }

    /**
     * @return mixed
     */
    public function getBanStartHourOld()
    {
        return $this->ban_start_hour_old;
    }

    /**
     * @param mixed $ban_start_hour_old
     */
    public function setBanStartHourOld($ban_start_hour_old)
    {
        $this->ban_start_hour_old = $ban_start_hour_old;
    }

    /**
     * @return mixed
     */
    public function getBanStartHourValue()
    {
        return $this->ban_start_hour_value;
    }

    /**
     * @param mixed $ban_start_hour_value
     */
    public function setBanStartHourValue($ban_start_hour_value)
    {
        $this->ban_start_hour_value = $ban_start_hour_value;
    }

    /**
     * @return mixed
     */
    public function getBanEndHourValue()
    {
        return $this->ban_end_hour_value;
    }

    /**
     * @param mixed $ban_end_hour_value
     */
    public function setBanEndHourValue($ban_end_hour_value)
    {
        $this->ban_end_hour_value = $ban_end_hour_value;
    }

    /**
     * @return mixed
     */
    public function getBanComplement()
    {
        return $this->ban_complement;
    }

    /**
     * @param mixed $ban_complement
     */
    public function setBanComplement($ban_complement)
    {
        $this->ban_complement = $ban_complement;
    }

}