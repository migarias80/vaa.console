<?php

namespace serviceimpl;

use daoimpl\DepartmentSQLDAO;
use daoimpl\EmpresaSQLDAO;
use daoimpl\FuncionSQLDAO;
use daoimpl\ParametroSQLDAO;
use daoimpl\UsuarioSQLDAO;
use dto\EmpresaDTO;
use dto\EmpresaEvalDTO;
use exception\EmpresaRepetidaException;
use model\UsuarioPerfil;
use service\IEmpresaService;
use model\Empresa;
use \utils\LogUtils;
use dto\UsuarioDTO;
use daoimpl\PersonaSQLDAO;
use serviceimpl\ParametroService;
use \Exception;
use \utils\SecurityUtils;
use model\Domain;
use daoimpl\DomainSQLDAO;
use dto\RutaMHCDTO;
use \utils\MHCParser;

class EmpresaService implements IEmpresaService  {

    private $empresaDAO;
    private $funcionDAO;
    private $usuarioDAO;
    private $parametroDAO;
    private $departamentoDAO;
    private $personasDAO;
    private $domainDAO;
	private $parametroService;

    function __construct() {
        $this->empresaDAO = new EmpresaSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
        $this->usuarioDAO = new UsuarioSQLDAO();
        $this->parametroDAO = new ParametroSQLDAO();
        $this->departamentoDAO = new DepartmentSQLDAO();
        $this->personasDAO = new PersonaSQLDAO();
        $this->parametroService = new ParametroService();
        $this->domainDAO = new DomainSQLDAO();
    }

    public function getEmpresaPorURL($urlName)
    {
        $empresas = $this->empresaDAO->getEmpresaPorURL($urlName);
        if ($empresas == null) {
            return null;
        }

        if (count($empresas) > 1) {
            return null;
        }

        return new EmpresaDTO($empresas[0]);
    }

    public function getEmpresa($id=null)
    {
        $empresas = $this->empresaDAO->getEmpresa($id);
        if ($empresas == null) {
            return null;
        }

        $empresasDTO = array();
        foreach ($empresas as $empresa) {
            $empresaDTO = new EmpresaDTO($empresa);
            if ($id != null) {
                $usuarios = $this->usuarioDAO->getUsuariosByIdEmpresa($empresaDTO->getId());
                $usuariosDTO = array();
                foreach ($usuarios as $usuario) {
                    $usuariosDTO[] = new UsuarioDTO($usuario);
                }
                $empresaDTO->setUsuarios($usuariosDTO);
            }
            $empresasDTO[] = $empresaDTO;
        }

        if ($id != null) {
            if (count($empresasDTO) == 1) {
                return $empresasDTO[0];
            } else {
                return array();
            }
        }
        return $empresasDTO;
    }

    public function guardar(EmpresaDTO $empresaDTO)
    {
        // Validacion de URL
        $empresaBuscada = $this->getEmpresaPorURL($empresaDTO->getUrlName());
        if ($empresaBuscada != null) {
            if ($empresaBuscada->getId() != $empresaDTO->getId()) {
                LogUtils::INFO("URL de empresa ya existente", __CLASS__, __FUNCTION__, array($empresaDTO->getUrlName()));
                throw new EmpresaRepetidaException($empresaDTO->getUrlName());
                return;
            }
        }

        // Guardado de la empresa
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setName($empresaDTO->getName());
        $empresa->setUrlName($empresaDTO->getUrlName());
        $empresa->setImg($empresaDTO->getImg());
        $empresa->setDnisRegex($empresaDTO->getDnisRegex());
        $empresa->setDnisRegexExt($empresaDTO->getDnisRegexExt());
        $empresa->setOutputRoute($empresaDTO->getOutputRoute());
        $empresa->setTts_mode($empresaDTO->getTts_mode());
        $idEmpresa = $this->empresaDAO->guardar($empresa);

        // Guardado del usuario solo cuando es una empresa nueva
        if ($empresaDTO->getId() == null) {
            $password = SecurityUtils::generarPassword("sa", $empresaDTO->getSaPassword());
            $this->usuarioDAO->nuevo("sa", $idEmpresa, "Sysadmin", $password, UsuarioPerfil::SA, null);
        }

        // Asignacion de bandas horarias solo cuando es una empresa nueva
        if ($empresaDTO->getId() == null) {
            $this->parametroDAO->generarBandasHorariasParaUnaEmpresa($idEmpresa);
        }

        // Creacion de parametros solo cuando es una empresa nueva
        if ($empresaDTO->getId() == null) {
            $this->parametroDAO->generarParametrosParaUnaEmpresa($idEmpresa);
        }

        // Guardado de Dial Plan por default
        if ($empresaDTO->getId() == null) {
            $domain = new Domain();
            $domain->setDom_regex("DEFAULT");
            $domain->setDom_domain("");
            $domain->setBusiness_id($idEmpresa);
            $domain->setDom_use_ani_ip_for_refer(1);
            $this->domainDAO->guardar($domain);
        }
    }

    public function eliminar($id)
    {
        $empresas = $this->empresaDAO->eliminar($id);
        if ($empresas == null) {
            return null;
        }
    }

    public function setImage(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setImg($empresaDTO->getImg());
        $this->empresaDAO->setImage($empresa);
    }

    // TODO: Obsoleto
    public function getEvaluacion($idEmpresa)
    {
        $departamentos = $this->departamentoDAO->getDepartment($idEmpresa);
        $evalDepartamentos = false;
        if (count($departamentos) > 0) {
            $evalDepartamentos = true;
        }

        $cantidadPersonas = $this->personasDAO->getCountPersonas($idEmpresa);
        $evalPersonas = false;
        if (intval($cantidadPersonas) > 0) {
            $evalPersonas = true;
        }

        $empresaEvaluacion = new EmpresaEvalDTO();
        $empresaEvaluacion->setId($idEmpresa);
        $empresaEvaluacion->setDepartamentosEstado($evalDepartamentos);
        $empresaEvaluacion->setPersonasEstado($evalPersonas);
        return $empresaEvaluacion;
    }

    public function setRegEx(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setDnisRegex($empresaDTO->getDnisRegex());
        $this->empresaDAO->setRegEx($empresa);
        
        $this->parametroService->evalHabilitarEmpresa($empresaDTO->getId());
    }

    public function setRegExExt(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setDnisRegexExt($empresaDTO->getDnisRegexExt());
        $this->empresaDAO->setRegExExt($empresa);

        $this->parametroService->evalHabilitarEmpresa($empresaDTO->getId());
    }

    public function setOutputRoute(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setOutputRoute($empresaDTO->getOutputRoute());
        $this->empresaDAO->setOutputRoute($empresa);

        $this->parametroService->evalHabilitarEmpresa($empresaDTO->getId());
    }

    public function getEvaluacionDNIS($nroEval)
    {
        $empresas = $this->empresaDAO->getEmpresa();
        if ($empresas == null) {
            return null;
        }

        $empresasDTO = array();
        foreach ($empresas as $empresa) {

            if ($empresa->getVaaActive() == "1") {
                $coincidencias = null;
                $coincidencias_ext = null;
                if ($empresa->getDnisRegex() != null && $empresa->getDnisRegex() != '') {
                    try {
                        preg_match('/'.$empresa->getDnisRegex().'/', $nroEval, $coincidencias);
                    } catch (Exception $e) {
                        
                    }
                }
                if ($empresa->getDnisRegexExt() != null && $empresa->getDnisRegexExt() != '') {
                    try {
                        preg_match('/'.$empresa->getDnisRegexExt().'/', $nroEval, $coincidencias_ext);
                    } catch (Exception $e) {
                        
                    }
                }
                
                if ($coincidencias =! null && count($coincidencias) > 0) {
                    $empresaDTO = new EmpresaDTO($empresa);
                    $empresaDTO->setOutputRoute($empresaDTO->getDnisRegex());
                    $empresaDTO->setDnisRegex(1);
                    $empresaDTO->setDnisRegexExt(0);
                    $empresasDTO[] = $empresaDTO;
                }
                if ($coincidencias_ext =! null && count($coincidencias_ext) > 0) {
                    $empresaDTO = new EmpresaDTO($empresa);
                    $empresaDTO->setOutputRoute($empresaDTO->getDnisRegexExt());
                    $empresaDTO->setDnisRegex(0);
                    $empresaDTO->setDnisRegexExt(1);
                    $empresasDTO[] = $empresaDTO;
                }
            }
        }

        return $empresasDTO;
    }

    public function setCantMaxPersonas(EmpresaDTO $empresaDTO)
    {
        $cantidadActualDePersonas = $this->personasDAO->getCountPersonas($empresaDTO->getId());
        if ($cantidadActualDePersonas > $empresaDTO->getCant_max_personas()) {
            throw new Exception("La cantidad de personas definidas debe ser mayor o igual a la consumida (actualmente " . $cantidadActualDePersonas . ")");
        }

        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setCant_max_personas($empresaDTO->getCant_max_personas());
        $this->empresaDAO->setCantMaxPersonas($empresa);
    }

    public function setCantMaxDepartamentos(EmpresaDTO $empresaDTO)
    {
        $cantidadActualDeDepartamentos = $this->departamentoDAO->getCountDepartments($empresaDTO->getId());
        if ($cantidadActualDeDepartamentos > $empresaDTO->getCant_max_departamentos()) {
            throw new Exception("La cantidad de departamentos definidos debe ser mayor o igual a la consumida (actualmente " . $cantidadActualDeDepartamentos . ")");
        }

        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setCant_max_departamentos($empresaDTO->getCant_max_departamentos());
        $this->empresaDAO->setCantMaxDepartamentos($empresa);
    }
    
    public function setCantMaxLineas(EmpresaDTO $empresaDTO)
    {
        $cantidadMaximaDeLineas = $this->parametroDAO->getParametros(1, ["CANT_MAX_LINES_GLOBAL"]);
        if (count($cantidadMaximaDeLineas) == 0) {
            $cantidadMaximaDeLineas = 0;
        } else {
            $cantidadMaximaDeLineas = $cantidadMaximaDeLineas[0];
            $cantidadMaximaDeLineas = $cantidadMaximaDeLineas->getParValue();
        }
        /* $cantidadActualDeLineasEntrantes = 0;
        $empresas = $this->getEmpresa();
        foreach ($empresas as $empresa) {
            if ($empresa->getVaaActive() == 1 && $empresa->getId() != $empresaDTO->getId()) {
                $cantidadActualDeLineasEntrantes += $empresa->getCantMaxLines();
            }
        }
        $cantidadActualDeLineasEntrantes += $empresaDTO->getCantMaxLines(); */
        if ($cantidadMaximaDeLineas < $empresaDTO->getCantMaxLines()) {
            throw new Exception("La cantidad de lineas entrantes definidas supera al límite general");
        }

        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setCantMaxLines($empresaDTO->getCantMaxLines());
        $this->empresaDAO->setCantMaxLineas($empresa);
    }

    public function setContacto(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setContacto($empresaDTO->getContacto());
        $this->empresaDAO->setContacto($empresa);
    }

    public function setNotas(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setNotas($empresaDTO->getNotas());
        $this->empresaDAO->setNotas($empresa);
    }

    public function setEnabled(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $this->empresaDAO->setEnabled($empresa);
    }

    public function setDisabled(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $this->empresaDAO->setDisabled($empresa);
    }

    public function setConfiguracionGeneral(EmpresaDTO $empresaDTO)
    {
        $cantidadActualDePersonas = $this->personasDAO->getCountPersonas($empresaDTO->getId());
        if ($cantidadActualDePersonas > $empresaDTO->getCant_max_personas()) {
            throw new Exception("La cantidad de personas definidas debe ser mayor o igual a la consumida (actualmente hay " . $cantidadActualDePersonas . " personas)");
        }

        $cantidadActualDeDepartamentos = $this->departamentoDAO->getCountDepartments($empresaDTO->getId());
        if ($cantidadActualDeDepartamentos > $empresaDTO->getCant_max_departamentos()) {
            throw new Exception("La cantidad de departamentos definidos debe ser mayor o igual a la consumida (actualmente hay " . $cantidadActualDeDepartamentos . " departamentos)");
        }

        $cantidadMaximaDeLineas = $this->parametroDAO->getParametros(1, ["CANT_MAX_LINES_GLOBAL"]);
        if (count($cantidadMaximaDeLineas) == 0) {
            $cantidadMaximaDeLineas = 0;
        } else {
            $cantidadMaximaDeLineas = $cantidadMaximaDeLineas[0];
            $cantidadMaximaDeLineas = $cantidadMaximaDeLineas->getParValue();
        }
        if ($cantidadMaximaDeLineas < $empresaDTO->getCantMaxLines()) {
            throw new Exception("La cantidad de lineas entrantes excede el límite de " . $cantidadMaximaDeLineas . " líneas");
        }

        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setDnisRegex($empresaDTO->getDnisRegex());
        $empresa->setDnisRegexExt($empresaDTO->getDnisRegexExt());
        $empresa->setOutputRoute($empresaDTO->getOutputRoute());
        $empresa->setVaaActive($empresaDTO->getVaaActive());
        $empresa->setCant_max_personas($empresaDTO->getCant_max_personas());
        $empresa->setCant_max_departamentos($empresaDTO->getCant_max_departamentos());
        $empresa->setCantMaxLines($empresaDTO->getCantMaxLines());
        $empresa->setContacto($empresaDTO->getContacto());
        $empresa->setNotas($empresaDTO->getNotas());

        // El tts mode se configura en forma individual
        // $empresa->setTts_mode($empresaDTO->getTts_mode());

        $this->empresaDAO->setConfiguracionGeneral($empresa);
    }

    public function setTTSMode(EmpresaDTO $empresaDTO)
    {
        $empresa = new Empresa();
        $empresa->setId($empresaDTO->getId());
        $empresa->setTts_mode($empresaDTO->getTts_mode());
        $this->empresaDAO->setTTSMode($empresa);
    }

    public function getRutasMHCDeEmpresa($id)
    {
        $empresas = $this->empresaDAO->getEmpresa($id);
        if ($empresas == null) {
            LogUtils::ERROR("Empresa no encontrada", __CLASS__, __FUNCTION__, array($id));
            throw new Exception("Empresa no encontrada");
        }

        $pathMHC = $this->parametroDAO->getParametros(1, ["MHC_FILE"]);
        if ($pathMHC == null) {
            LogUtils::INFO("La ruta del archivo MHC no se encuentra configurada", __CLASS__, __FUNCTION__, array($id));
            return [];
        } else {
            $pathMHC = $pathMHC[0];
            $pathMHC = $pathMHC->getParValue();    
        }

        try {
            return MHCParser::GetRoutes($pathMHC);
        } catch (Exception $exc) {
            LogUtils::INFO("Se produjo un error al procesar el archivo MHC", __CLASS__, __FUNCTION__, array($id));
            return [];
        }
    }

}