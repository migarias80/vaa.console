<?php

namespace dto;

use \model\Usuario;
use \utils\CommonUtils;

class UsuarioDTO implements IGenericDTO
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

    function __construct(Usuario $usuario=null)
    {
        if ($usuario == null) { return; }
        $this->setId($usuario->getId());
        $this->setNombreUsuario($usuario->getNombreUsuario());
        $this->setPassword($usuario->getPassword());
        $this->setIdEmpresa($usuario->getIdEmpresa());
        $this->setFullName($usuario->getFullName());
        // $this->setLastAccess($usuario->getLastAccess());
        $this->setIdProfile($usuario->getIdProfile());
        $this->setLastEditUserId($usuario->getLastEditUserId());
        $this->setActive($usuario->getActive());
    }

    public function constructFromArray($data) {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }
        if (array_key_exists('nombreUsuario', $data)) {
            $this->setNombreUsuario($data['nombreUsuario']);
        }
        if (array_key_exists('idEmpresa', $data)) {
            $this->setIdEmpresa($data['idEmpresa']);
        }
        if (array_key_exists('fullName', $data)) {
            $this->setFullName($data['fullName']);
        }
        /*if (array_key_exists('lastAccess', $data)) {
            $this->setLastAccess($data['lastAccess']);
        }*/
        if (array_key_exists('idProfile', $data)) {
            $this->setIdProfile($data['idProfile']);
        }
        if (array_key_exists('password', $data)) {
            $this->setPassword($data['password']);
        }
        if (array_key_exists('lastEditUserId', $data)) {
            $this->setLastEditUserId($data['lastEditUserId']);
        }
        if (array_key_exists('active', $data)) {
            $this->setActive($data['active']);
        }
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