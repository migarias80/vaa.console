<?php

namespace dto;

class LoginDTO
{

    private $id;
    private $nombreUsuario;
    private $password;
    private $urlEmpresa;
    private $idEmpresa;

    function __construct($data=null)
    {
        if ($data == null) { return; }
        $has = get_object_vars($this);
        foreach ($has as $name => $oldValue) {
            $this->$name = isset($data[$name]) ? $data[$name] : NULL;
        }
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
    public function getUrlEmpresa()
    {
        return $this->urlEmpresa;
    }

    /**
     * @param mixed $urlEmpresa
     */
    public function setUrlEmpresa($urlEmpresa)
    {
        $this->urlEmpresa = $urlEmpresa;
    }

}