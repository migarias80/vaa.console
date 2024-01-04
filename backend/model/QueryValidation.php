<?php

namespace model;

use \utils\CommonUtils;

class QueryValidation
{

    private $value;
    private $desc;

    function __construct($data = null)
    {
        if ($data == null) {
            return;
        }
        $this->setValue(CommonUtils::GetArrayValue('VALUE', $data));
        $this->setDesc(CommonUtils::GetArrayValue('DESC', $data));
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param mixed $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

}