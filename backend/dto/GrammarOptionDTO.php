<?php

namespace dto;

use model\GrammarOption;
use utils\CommonUtils;

class GrammarOptionDTO implements IGenericDTO {

    private $oig_id;
    private $oig_description;

    function __construct(GrammarOption $option = null)
    {
        if ($option == null) { return; }
        $this->setOigId($option->getOigId());
        $this->setOigDescription($option->getOigDescription());
    }

    function constructFromArray($data)
    {
        $this->setOigId(CommonUtils::GetArrayValue($data, 'OIG_ID'));
        $this->setOigDescription(CommonUtils::GetArrayValue($data, 'OIG_DESCRIPTION'));
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