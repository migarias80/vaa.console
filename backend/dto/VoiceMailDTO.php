<?php

namespace dto;

use \model\VoiceMail;
use \utils\CommonUtils;

class VoiceMailDTO implements IGenericDTO {

    private $vma_id;
    private $vma_description;
    private $vma_internal_number;
    private $vma_enabled_daytime;
    private $vma_enabled_nighttime;
    private $vma_allow_dial_post;
    private $vma_default_dialed_number;
    private $vma_digits;
    private $vma_last_update_utc;
    private $business_id;
	private $COMPANY_ID;

    function __construct(VoiceMail $voiceMail = null) {
        if ($voiceMail == null) { return; }
        $this->setVma_id($voiceMail->getVma_id());
        $this->setVma_description($voiceMail->getVma_description());
        $this->setVma_internal_number($voiceMail->getVma_internal_number());
        $this->setVma_enabled_daytime($voiceMail->getVma_enabled_daytime());
        $this->setVma_enabled_nighttime($voiceMail->getVma_enabled_nighttime());
        $this->setVma_allow_dial_post($voiceMail->getVma_allow_dial_post());
        $this->setVma_default_dialed_number($voiceMail->getVma_default_dialed_number());
        $this->setVma_digits($voiceMail->getVma_digits());
        $this->setVma_last_update_utc($voiceMail->getVma_last_update_utc());
        $this->setBusiness_id($voiceMail->getBusiness_id());
    }

    public function constructFromArray($data) {
        $this->setVma_id(CommonUtils::GetArrayValue('VMA_ID', $data));
        $this->setVma_description(CommonUtils::GetArrayValue('VMA_DESCRIPTION', $data));
        $this->setVma_internal_number(CommonUtils::GetArrayValue('VMA_INTERNAL_NUMBER', $data));
        $this->setVma_enabled_daytime(CommonUtils::GetArrayValue('VMA_ENABLED_DAYTIME', $data));
        $this->setVma_enabled_nighttime(CommonUtils::GetArrayValue('VMA_ENABLED_NIGHTTIME', $data));
        $this->setVma_allow_dial_post(CommonUtils::GetArrayValue('VMA_ALLOW_DIAL_POST', $data));
        $this->setVma_default_dialed_number(CommonUtils::GetArrayValue('VMA_DEFAULT_DIALED_NUMBER', $data));
        $this->setVma_digits(CommonUtils::GetArrayValue('VMA_DIGITS', $data));
        $this->setVma_last_update_utc(CommonUtils::GetArrayValue('VMA_LAST_UPDATE_UTC', $data));
        $this->setBusiness_id(CommonUtils::GetArrayValue('COMPANY_ID', $data));
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

    public function getVma_id() {
        return $this->vma_id;
    }

    public function setVma_id($vma_id) {
        $this->vma_id = $vma_id;
    }


    public function getVma_description() {
        return $this->vma_description;
    }

    public function setVma_description($vma_description) {
        $this->vma_description = $vma_description;
    }


    public function getVma_internal_number() {
        return $this->vma_internal_number;
    }

    public function setVma_internal_number($vma_internal_number) {
        $this->vma_internal_number = $vma_internal_number;
    }


    public function getVma_enabled_daytime() {
        return $this->vma_enabled_daytime;
    }

    public function setVma_enabled_daytime($vma_enabled_daytime) {
        $this->vma_enabled_daytime = $vma_enabled_daytime;
    }


    public function getVma_enabled_nighttime() {
        return $this->vma_enabled_nighttime;
    }

    public function setVma_enabled_nighttime($vma_enabled_nighttime) {
        $this->vma_enabled_nighttime = $vma_enabled_nighttime;
    }


    public function getVma_allow_dial_post() {
        return $this->vma_allow_dial_post;
    }

    public function setVma_allow_dial_post($vma_allow_dial_post) {
        $this->vma_allow_dial_post = $vma_allow_dial_post;
    }


    public function getVma_default_dialed_number() {
        return $this->vma_default_dialed_number;
    }

    public function setVma_default_dialed_number($vma_default_dialed_number) {
        $this->vma_default_dialed_number = $vma_default_dialed_number;
    }


    public function getVma_digits() {
        return $this->vma_digits;
    }

    public function setVma_digits($vma_digits) {
        $this->vma_digits = $vma_digits;
    }


    public function getVma_last_update_utc() {
        return $this->vma_last_update_utc;
    }

    public function setVma_last_update_utc($vma_last_update_utc) {
        $this->vma_last_update_utc = $vma_last_update_utc;
    }


    public function getBusiness_id() {
        return $this->COMPANY_ID;
    }

    public function setBusiness_id($business_id) {
        $this->COMPANY_ID = $business_id;
    }
}