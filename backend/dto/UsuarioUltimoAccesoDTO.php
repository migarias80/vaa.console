<?php

namespace dto;

use \model\UsuarioUltimoAcceso;
use \utils\CommonUtils;

class UsuarioUltimoAccesoDTO implements IGenericDTO {

    private $user_id;
    private $business_id;
    private $last_access;
    private $ip_address;

    private $business_name;

    function __construct(UsuarioUltimoAcceso $usuarioUltimoAcceso=null)
    {
        if ($usuarioUltimoAcceso == null) { return; }
        if (is_array($usuarioUltimoAcceso)) {
            $this->setUserId(CommonUtils::GetArrayValue('USER_ID', $usuarioUltimoAcceso));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $usuarioUltimoAcceso));
            $this->setLastAccess(CommonUtils::GetArrayValue('LAST_ACCESS', $usuarioUltimoAcceso));
            $this->setIpAddress(CommonUtils::GetArrayValue('IP_ADDRESS', $usuarioUltimoAcceso));
            $this->setBusinessName(CommonUtils::GetArrayValue('BUSINESS_NAME', $data));
        } else if ($usuarioUltimoAcceso instanceof UsuarioUltimoAcceso) {
            $this->setUserId($usuarioUltimoAcceso->getUserId());
            $this->setBusinessId($usuarioUltimoAcceso->getBusinessId());
            $this->setLastAccess($usuarioUltimoAcceso->getLastAccess());
            $this->setIpAddress($usuarioUltimoAcceso->getIpAddress());
            $this->setBusinessName($usuarioUltimoAcceso->getBusinessName());
        }
    }

    public function constructFromArray($data) {
        $this->setUserId(CommonUtils::GetArrayValue('USER_ID', $data));
        $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        $this->setLastAccess(CommonUtils::GetArrayValue('LAST_ACCESS', $data));
        $this->setIpAddress(CommonUtils::GetArrayValue('IP_ADDRESS', $data));
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

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->business_id;
    }

    /**
     * @param mixed $business_id
     */
    public function setBusinessId($business_id)
    {
        $this->business_id = $business_id;
    }

    /**
     * @return mixed
     */
    public function getLastAccess()
    {
        return $this->last_access;
    }

    /**
     * @param mixed $last_access
     */
    public function setLastAccess($last_access)
    {
        $this->last_access = $last_access;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * @param mixed $ip_address
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = $ip_address;
    }

    /**
     * @return mixed
     */
    public function getBusinessName()
    {
        return $this->business_name;
    }

    /**
     * @param mixed $business_name
     */
    public function setBusinessName($business_name)
    {
        $this->business_name = $business_name;
    }
}