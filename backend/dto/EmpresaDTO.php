<?php

namespace dto;

use model\Empresa;
use \utils\CommonUtils;

class EmpresaDTO implements IGenericDTO
{

    private $id;
    private $name;
    private $url_name;
    private $img;
    private $dnis_regex;
    private $output_route;
    private $dnis_regex_ext;
    private $usuarios;
    private $saPassword;
    private $vaa_active; // campo COM_ENABLED
    private $cant_max_lines;
    private $cant_max_departamentos;
    private $cant_max_personas;
    private $contacto;
    private $notas;
    private $tts_mode;
    
    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setId(CommonUtils::GetArrayValue('ID', $data));
            $this->setName(CommonUtils::GetArrayValue('NAME', $data));
            $this->setUrlName(CommonUtils::GetArrayValue('URL_NAME', $data));
            $this->setImg(CommonUtils::GetArrayValue('IMG', $data));
            $this->setSaPassword(CommonUtils::GetArrayValue('PASSWORD', $data));
            $this->setVaaActive(CommonUtils::GetArrayValue('VAA_ACTIVE', $data));
            $this->setCantMaxLines(CommonUtils::GetArrayValue('CANT_MAX_LINES', $data));
            $this->setDnisRegex(CommonUtils::GetArrayValue('DNIS_REGEX', $data));
            $this->setOutputRoute(CommonUtils::GetArrayValue('OUTPUT_ROUTE', $data));
            $this->setDnisRegexExt(CommonUtils::GetArrayValue('DNIS_REGEX_EXT', $data));
            $this->setCant_max_personas(CommonUtils::GetArrayValue('CANT_MAX_PERSONAS', $data));
            $this->setCant_max_departamentos(CommonUtils::GetArrayValue('CANT_MAX_DEPARTAMENTOS', $data));
            $this->setContacto(CommonUtils::GetArrayValue('CONTACTO', $data));
            $this->setNotas(CommonUtils::GetArrayValue('NOTAS', $data));
            $this->setTts_mode(CommonUtils::GetArrayValue('TTS_MODE', $data));
        } else if ($data instanceof Empresa) {
            $this->setId($data->getId());
            $this->setName($data->getName());
            $this->setUrlName($data->getUrlName());
            $this->setImg($data->getImg());
            $this->setVaaActive($data->getVaaActive());
            $this->setCantMaxLines($data->getCantMaxLines());
            $this->setDnisRegex($data->getDnisRegex());
            $this->setOutputRoute($data->getOutputRoute());
            $this->setDnisRegexExt($data->getDnisRegexExt());
            $this->setCant_max_personas($data->getCant_max_personas());
            $this->setCant_max_departamentos($data->getCant_max_departamentos());
            $this->setContacto($data->getContacto());
            $this->setNotas($data->getNotas());
            $this->setTts_mode($data->getTts_mode());
        }

    }

    public function constructFromArray($data)
    {
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
        $this->setName(CommonUtils::GetArrayValue('NAME', $data));
        $this->setUrlName(CommonUtils::GetArrayValue('URL_NAME', $data));
        $this->setImg(CommonUtils::GetArrayValue('IMG', $data));
        $this->setDnisRegex(CommonUtils::GetArrayValue('DNIS_REGEX', $data));
        $this->setOutputRoute(CommonUtils::GetArrayValue('OUTPUT_ROUTE', $data));
        $this->setDnisRegexExt(CommonUtils::GetArrayValue('DNIS_REGEX_EXT', $data));
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

    public function constructFromJSON($data) {
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUrlName()
    {
        return $this->url_name;
    }

    /**
     * @param mixed $urlName
     */
    public function setUrlName($url_name)
    {
        $this->url_name = $url_name;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img)
    {
        $this->img = $img;
    }

    /**
     * @return mixed
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * @param mixed $usuarios
     */
    public function setUsuarios($usuarios)
    {
        $this->usuarios = $usuarios;
    }

    /**
     * @return mixed
     */
    public function getSaPassword()
    {
        return $this->saPassword;
    }

    /**
     * @param mixed $saPassword
     */
    public function setSaPassword($saPassword)
    {
        $this->saPassword = $saPassword;
    }

    /**
     * @return mixed
     */
    public function getVaaActive()
    {
        return $this->vaa_active;
    }

    /**
     * @param mixed $vaa_active
     */
    public function setVaaActive($vaa_active)
    {
        $this->vaa_active = $vaa_active;
    }

    /**
     * @return mixed
     */
    public function getCantMaxLines()
    {
        return $this->cant_max_lines;
    }

    /**
     * @param mixed $cant_max_lines
     */
    public function setCantMaxLines($cant_max_lines)
    {
        $this->cant_max_lines = $cant_max_lines;
    }

    /**
     * @return mixed
     */
    public function getDnisRegex()
    {
        return $this->dnis_regex;
    }

    /**
     * @param mixed $dnis_regex
     */
    public function setDnisRegex($dnis_regex)
    {
        $this->dnis_regex = $dnis_regex;
    }

    /**
     * @return mixed
     */
    public function getOutputRoute()
    {
        return $this->output_route;
    }

    /**
     * @param mixed $output_route
     */
    public function setOutputRoute($output_route)
    {
        $this->output_route = $output_route;
    }

    public function getDnisRegexExt()
    {
        return $this->dnis_regex_ext;
    }

    public function setDnisRegexExt($dnis_regex_ext)
    {
        $this->dnis_regex_ext = $dnis_regex_ext;
    }

    public function getCant_max_departamentos(){
		return $this->cant_max_departamentos;
	}

	public function setCant_max_departamentos($cant_max_departamentos){
		$this->cant_max_departamentos = $cant_max_departamentos;
	}

	public function getCant_max_personas(){
		return $this->cant_max_personas;
	}

	public function setCant_max_personas($cant_max_personas){
		$this->cant_max_personas = $cant_max_personas;
	}

	public function getContacto(){
		return $this->contacto;
	}

	public function setContacto($contacto){
		$this->contacto = $contacto;
    }
    
	public function getNotas(){
		return $this->notas;
	}

	public function setNotas($notas){
		$this->notas = $notas;
    }
    
    public function getTts_mode(){
		return $this->tts_mode;
	}

	public function setTts_mode($tts_mode){
		$this->tts_mode = $tts_mode;
	}

}