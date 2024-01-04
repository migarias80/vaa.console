<?php

namespace model;

use \utils\CommonUtils;

class GrammarOption
{

    private $oig_id;
    private $oig_description;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setOigId(CommonUtils::GetArrayValue('OIG_ID', $data));
        $this->setOigDescription(CommonUtils::GetArrayValue('OIG_DESCRIPTION', $data));
    }

    /**
     * @return mixed
     */
    public function getOigId()
    {
        return $this->oig_id;
    }

    /**
     * @param mixed $oig_id
     */
    public function setOigId($oig_id)
    {
        $this->oig_id = $oig_id;
    }

    /**
     * @return mixed
     */
    public function getOigDescription()
    {
        return $this->oig_description;
    }

    /**
     * @param mixed $oig_description
     */
    public function setOigDescription($oig_description)
    {
        $this->oig_description = $oig_description;
    }

}