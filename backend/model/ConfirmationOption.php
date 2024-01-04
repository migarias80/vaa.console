<?php

namespace model;

use \utils\CommonUtils;

class ConfirmationOption
{

    private $oco_id;
    private $oco_description;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setOco_id(CommonUtils::GetArrayValue('OCO_ID', $data));
        $this->setOco_description(CommonUtils::GetArrayValue('OCO_DESCRIPTION', $data));
    }

    public function getOco_id() {
        return $this->oco_id;
    }

    public function setOco_id($oco_id) {
        $this->oco_id = $oco_id;
    }


    public function getOco_description() {
        return $this->oco_description;
    }

    public function setOco_description($oco_description) {
        $this->oco_description = $oco_description;
    }

}