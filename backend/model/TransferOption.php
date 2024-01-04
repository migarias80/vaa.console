<?php

namespace model;

use \utils\CommonUtils;

class TransferOption
{

    private $tao_id;
    private $tao_category;
    private $tao_description;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setTaoId(CommonUtils::GetArrayValue('TAO_ID', $data));
        $this->setTaoCategory(CommonUtils::GetArrayValue('TAO_CATEGORY', $data));
        $this->setTaoDescription(CommonUtils::GetArrayValue('TAO_DESCRIPTION', $data));
    }

    /**
     * @return mixed
     */
    public function getTaoId()
    {
        return $this->tao_id;
    }

    /**
     * @param mixed $tao_id
     */
    public function setTaoId($tao_id)
    {
        $this->tao_id = $tao_id;
    }

    /**
     * @return mixed
     */
    public function getTaoCategory()
    {
        return $this->tao_category;
    }

    /**
     * @param mixed $tao_category
     */
    public function setTaoCategory($tao_category)
    {
        $this->tao_category = $tao_category;
    }

    /**
     * @return mixed
     */
    public function getTaoDescription()
    {
        return $this->tao_description;
    }

    /**
     * @param mixed $tao_description
     */
    public function setTaoDescription($tao_description)
    {
        $this->tao_description = $tao_description;
    }

}