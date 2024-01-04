<?php

namespace dto;

use model\Empresa;
use \utils\CommonUtils;

class RutaMHCDTO implements IGenericDTO
{

    private $line_setting_id;
    private $ruta;
    
    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setLine_setting_id(CommonUtils::GetArrayValue('LINE_SETTING_ID', $data));
            $this->setRuta(CommonUtils::GetArrayValue('RUTA', $data));
        }
    }

    public function constructFromArray($data)
    {
        $this->setLine_setting_id(CommonUtils::GetArrayValue('LINE_SETTING_ID', $data));
        $this->setRuta(CommonUtils::GetArrayValue('RUTA', $data));
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

    public function getLine_setting_id()
    {
        return $this->line_setting_id;
    }

    public function setLine_setting_id($line_setting_id)
    {
        $this->line_setting_id = $line_setting_id;
    }

    public function getRuta()
    {
        return $this->ruta;
    }

    public function setRuta($ruta)
    {
        $this->ruta = $ruta;
    }

}