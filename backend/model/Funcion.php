<?php

namespace model;

use \utils\CommonUtils;

class Funcion
{

    private $id;
    private $desc;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
        $this->setDesc(CommonUtils::GetArrayValue('NAME', $data));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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