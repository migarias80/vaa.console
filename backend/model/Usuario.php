<?php

namespace model;

use \utils\CommonUtils;

class Usuario
{

    private $id;
    private $nombreUsuario;
    private $password;
    private $idEmpresa;
    private $fullName;
    // private $lastAccess;
    private $idProfile;
    private $lastEditUserId;
    private $active;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
        $this->setNombreUsuario(CommonUtils::GetArrayValue('NAME', $data));
        $this->setPassword(CommonUtils::GetArrayValue('PASSWORD', $data));
        $this->setIdEmpresa(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        $this->setFullName(CommonUtils::GetArrayValue('FULL_NAME', $data));
        // $this->setLastAccess(CommonUtils::GetArrayValue('LAST_ACCESS', $data));
        $this->setIdProfile(CommonUtils::GetArrayValue('ID_PROFILE', $data));
        $this->setLastEditUserId(CommonUtils::GetArrayValue('LAST_EDIT_USER_ID', $data));
        $this->setActive(CommonUtils::GetArrayValue('ACTIVE', $data));
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * @param mixed $nombreUsuario
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * @param mixed $idEmpresa
     */
    public function setIdEmpresa($idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    /*public function getLastAccess()
    {
        return $this->lastAccess;
    }*/

    /**
     * @param mixed $lastAccess
     */
    /*public function setLastAccess($lastAccess)
    {
        $this->lastAccess = $lastAccess;
    }*/

    /**
     * @return mixed
     */
    public function getIdProfile()
    {
        return $this->idProfile;
    }

    /**
     * @param mixed $idProfile
     */
    public function setIdProfile($idProfile)
    {
        $this->idProfile = $idProfile;
    }

    /**
     * @return mixed
     */
    public function getLastEditUserId()
    {
        return $this->lastEditUserId;
    }

    /**
     * @param mixed $lastEditUserId
     */
    public function setLastEditUserId($lastEditUserId)
    {
        $this->lastEditUserId = $lastEditUserId;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

}