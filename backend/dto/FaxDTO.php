<?php

namespace dto;

use \model\Fax;
use \utils\CommonUtils;
use utils\ControllerUtils;

class FaxDTO implements IGenericDTO {

    private $fax_id;
    private $fax_description;
    private $fax_internal_number;
    private $fax_enabled_daytime;
    private $fax_enabled_nighttime;
    private $fax_allow_dial_post;
    private $fax_default_dialed_number;
    private $fax_digits;
    private $fax_last_update_utc;
    private $business_id;
	private $COMPANY_ID;

    function __construct(Fax $fax = null) {
        if ($fax == null) { return; }
        $this->setFax_id($fax->getFax_id());
        $this->setFax_description($fax->getFax_description());
        $this->setFax_internal_number($fax->getFax_internal_number());
        $this->setFax_enabled_daytime($fax->getFax_enabled_daytime());
        $this->setFax_enabled_nighttime($fax->getFax_enabled_nighttime());
        $this->setFax_allow_dial_post($fax->getFax_allow_dial_post());
        $this->setFax_default_dialed_number($fax->getFax_default_dialed_number());
        $this->setFax_digits($fax->getFax_digits());
        $this->setFax_last_update_utc($fax->getFax_last_update_utc());
        $this->setBusiness_id($fax->getBusiness_id());
    }

    public function constructFromArray($data) {
        $this->setFax_id(CommonUtils::GetArrayValue('FAX_ID', $data));
        $this->setFax_description(CommonUtils::GetArrayValue('FAX_DESCRIPTION', $data));
        $this->setFax_internal_number(CommonUtils::GetArrayValue('FAX_INTERNAL_NUMBER', $data));
        $this->setFax_enabled_daytime(CommonUtils::GetArrayValue('FAX_ENABLED_DAYTIME', $data));
        $this->setFax_enabled_nighttime(CommonUtils::GetArrayValue('FAX_ENABLED_NIGHTTIME', $data));
        $this->setFax_allow_dial_post(CommonUtils::GetArrayValue('FAX_ALLOW_DIAL_POST', $data));
        $this->setFax_default_dialed_number(CommonUtils::GetArrayValue('FAX_DEFAULT_DIALED_NUMBER', $data));
        $this->setFax_digits(CommonUtils::GetArrayValue('FAX_DIGITS', $data));
        $this->setFax_last_update_utc(CommonUtils::GetArrayValue('FAX_LAST_UPDATE_UTC', $data));
        $this->setBusiness_id(CommonUtils::GetArrayValue('COMPANY_ID', $data));
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

    public function getFax_id() {
        return $this->fax_id;
    }

    public function setFax_id($fax_id) {
        $this->fax_id = $fax_id;
    }


    public function getFax_description() {
        return $this->fax_description;
    }

    public function setFax_description($fax_description) {
        $this->fax_description = $fax_description;
    }


    public function getFax_internal_number() {
        return $this->fax_internal_number;
    }

    public function setFax_internal_number($fax_internal_number) {
        $this->fax_internal_number = $fax_internal_number;
    }


    public function getFax_enabled_daytime() {
        return $this->fax_enabled_daytime;
    }

    public function setFax_enabled_daytime($fax_enabled_daytime) {
        $this->fax_enabled_daytime = $fax_enabled_daytime;
    }


    public function getFax_enabled_nighttime() {
        return $this->fax_enabled_nighttime;
    }

    public function setFax_enabled_nighttime($fax_enabled_nighttime) {
        $this->fax_enabled_nighttime = $fax_enabled_nighttime;
    }


    public function getFax_allow_dial_post() {
        return $this->fax_allow_dial_post;
    }

    public function setFax_allow_dial_post($fax_allow_dial_post) {
        $this->fax_allow_dial_post = $fax_allow_dial_post;
    }

    public function getFax_default_dialed_number() {
        return $this->fax_default_dialed_number;
    }

    public function setFax_default_dialed_number($fax_default_dialed_number) {
        $this->fax_default_dialed_number = $fax_default_dialed_number;
    }


    public function getFax_digits() {
        return $this->fax_digits;
    }

    public function setFax_digits($fax_digits) {
        $this->fax_digits = $fax_digits;
    }


    public function getFax_last_update_utc() {
        return $this->fax_last_update_utc;
    }

    public function setFax_last_update_utc($fax_last_update_utc) {
        $this->fax_last_update_utc = $fax_last_update_utc;
    }


    public function getBusiness_id() {
        return $this->COMPANY_ID;
    }

    public function setBusiness_id($business_id) {
        $this->COMPANY_ID = $business_id;
    }

}
