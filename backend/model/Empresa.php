<?php

namespace model;

use \utils\CommonUtils;

class Empresa
{

    private $id;
    private $name;
    private $urlName;
    private $img;
    private $dnisRegex;
    private $outputRoute;
    private $dnisRegexExt;
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
        $this->setId(CommonUtils::GetArrayValue('ID', $data));
        $this->setName(CommonUtils::GetArrayValue('NAME', $data));
        $this->setUrlName(CommonUtils::GetArrayValue('URL_NAME', $data));
        $this->setImg(CommonUtils::GetArrayValue('IMG', $data));
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
        return $this->urlName;
    }

    /**
     * @param mixed $urlName
     */
    public function setUrlName($urlName)
    {
        $this->urlName = $urlName;
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
        return $this->dnisRegex;
    }

    /**
     * @param mixed $dnisRegex
     */
    public function setDnisRegex($dnisRegex)
    {
        $this->dnisRegex = $dnisRegex;
    }

    /**
     * @return mixed
     */
    public function getOutputRoute()
    {
        return $this->outputRoute;
    }

    /**
     * @param mixed $outputRoute
     */
    public function setOutputRoute($outputRoute)
    {
        $this->outputRoute = $outputRoute;
    }

    public function getDnisRegexExt()
    {
        return $this->dnisRegexExt;
    }

    public function setDnisRegexExt($dnisRegexExt)
    {
        $this->dnisRegexExt = $dnisRegexExt;
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