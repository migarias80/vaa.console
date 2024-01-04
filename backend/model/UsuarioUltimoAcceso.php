<?php

namespace model;

use dto\UsuarioUltimoAccesoDTO;
use \utils\CommonUtils;

class UsuarioUltimoAcceso
{

    private $user_id;
    private $business_id;
    private $last_access;
    private $ip_address;

    private $business_name;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setUserId(CommonUtils::GetArrayValue('USER_ID', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
            $this->setLastAccess(CommonUtils::GetArrayValue('LAST_ACCESS', $data));
            $this->setIpAddress(CommonUtils::GetArrayValue('IP_ADDRESS', $data));
            $this->setBusinessName(CommonUtils::GetArrayValue('BUSINESS_NAME', $data));
        } else if ($data instanceof UsuarioUltimoAccesoDTO) {
            $this->setUserId($data->getUserId());
            $this->setBusinessId($data->getBusinessId());
            $this->setLastAccess($data->getLastAccess());
            $this->setIpAddress($data->getIpAddress());
            $this->setBusinessName($data->getBusinessName());
        }
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