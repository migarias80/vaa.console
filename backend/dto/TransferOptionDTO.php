<?php

namespace dto;

use \model\TransferOption;
use utils\CommonUtils;

class TransferOptionDTO implements IGenericDTO {

    private $tao_id;
    private $tao_category;
    private $tao_description;

    function __construct(TransferOption $option = null)
    {
        if ($option == null) { return; }
        $this->setTaoId($option->getTaoId());
        $this->setTaoCategory($option->getTaoCategory());
        $this->setTaoDescription($option->getTaoDescription());
    }

    function constructFromArray($data)
    {
        $this->setTaoId(CommonUtils::GetArrayValue($data, 'TAO_ID'));
        $this->setTaoCategory(CommonUtils::GetArrayValue($data, 'TAO_CATEGORY'));
        $this->setTaoDescription(CommonUtils::GetArrayValue($data, 'TAO_DESCRIPTION'));
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
}