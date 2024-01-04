<?php

namespace model;

use \utils\CommonUtils;

class UsuarioAU
{

    private $id;
    private $nombreUsuario;
    private $password;
    private $idEmpresa;
    private $fullName;
    private $idProfile;
    private $lastEditUserId;
    private $lastEdit;
    private $lastEditUserName;
    private $changePassword;
    private $active;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
        $this->setNombreUsuario(CommonUtils::GetArrayValue('NAME', $data));
        $this->setPassword(CommonUtils::GetArrayValue('PASSWORD', $data));
        $this->setIdEmpresa(CommonUtils::GetArrayValue('COMPANY_ID', $data));
        $this->setFullName(CommonUtils::GetArrayValue('FULL_NAME', $data));
        $this->setIdProfile(CommonUtils::GetArrayValue('ID_PROFILE', $data));
        $this->setLastEditUserId(CommonUtils::GetArrayValue('LAST_EDIT_USER_ID', $data));
        $this->setLastEdit(CommonUtils::GetArrayValue('LAST_EDIT', $data));
        $this->setLastEditUserName(CommonUtils::GetArrayValue('LAST_EDIT_USER_NAME', $data));
        $this->setLastEditUserName(CommonUtils::GetArrayValue('LAST_EDIT_USER_NAME', $data));
        $this->setChangePassword(CommonUtils::GetArrayValue('CHANGE_PASSWORD', $data));
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
    public function getLastEdit()
    {
        return $this->lastEdit;
    }

    /**
     * @param mixed $lastEdit
     */
    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;
    }

    /**
     * @return mixed
     */
    public function getLastEditUserName()
    {
        return $this->lastEditUserName;
    }

    /**
     * @param mixed $lastEditUserName
     */
    public function setLastEditUserName($lastEditUserName)
    {
        $this->lastEditUserName = $lastEditUserName;
    }

    /**
     * @return mixed
     */
    public function getChangePassword()
    {
        return $this->changePassword;
    }

    /**
     * @param mixed $changePassword
     */
    public function setChangePassword($changePassword)
    {
        $this->changePassword = $changePassword;
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