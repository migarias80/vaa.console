<?php

namespace serviceimpl;

use daoimpl\FuncionSQLDAO;
use dto\FaxDTO;
use model\Fax;
use service\IFaxService;
use daoimpl\FaxSQLDAO;
use serviceimpl\DepartmentService;
use serviceimpl\PersonaService;
use serviceimpl\ParametroService;
use \Exception;

class FaxService implements IFaxService
{

    private $faxDAO;
    private $funcionDAO;
    private $departamentoService;
    private $personaService;

    function __construct() {
        $this->faxDAO = new FaxSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
    }

    function getFax($idEmpresa, $id=null) {
        $faxes = $this->faxDAO->getFax($idEmpresa, $id);
        if ($faxes == null) {
            return null;
        }

        $faxesDTO = array();
        foreach ($faxes as $fax) {
            $faxesDTO[] = new FaxDTO($fax);
        }
        return $faxesDTO;
    }

    function guardar(FaxDTO $faxDTO) {
        if ($faxDTO->getFax_id() != null) {
            $this->departamentoService = new DepartmentService();
            $this->personaService = new PersonaService();
            $departamentosConElFax_Day = $this->departamentoService->getDepartment($faxDTO->getBusiness_id(), null, null, false, true, $faxDTO->getFax_id());
            $departamentosConElFax_Night = $this->departamentoService->getDepartment($faxDTO->getBusiness_id(), null, null, false, true, null, $faxDTO->getFax_id());
            $personasConElFax_Day = $this->personaService->getPersona($faxDTO->getBusiness_id(), null, null, false, true, $faxDTO->getFax_id());
            $personasConElFax_Night = $this->personaService->getPersona($faxDTO->getBusiness_id(), null, null, false, true, null, $faxDTO->getFax_id());
            if (!$faxDTO->getFax_enabled_daytime() && count($departamentosConElFax_Day) > 0) {
                throw new Exception("No es posible actualizar el fax ya que se encuentra configurado como diurno en al menos un departamento");
            }
            if (!$faxDTO->getFax_enabled_nighttime() && count($departamentosConElFax_Night) > 0) {
                throw new Exception("No es posible actualizar el fax ya que se encuentra configurado como nocturno en al menos un departamento");
            }
            if (!$faxDTO->getFax_enabled_daytime() && count($personasConElFax_Day) > 0) {
                throw new Exception("No es posible actualizar el fax ya que se encuentra configurado como diurno en al menos una persona");
            }
            if (!$faxDTO->getFax_enabled_nighttime() && count($personasConElFax_Night) > 0) {
                throw new Exception("No es posible actualizar el fax ya que se encuentra configurado como nocturno en al menos una persona");
            }
        }

        $fax = new Fax();
        $fax->setFax_id($faxDTO->getFax_id());
        $fax->setFax_description($faxDTO->getFax_description());
        $fax->setFax_internal_number($faxDTO->getFax_internal_number());
        $fax->setFax_enabled_daytime($faxDTO->getFax_enabled_daytime());
        $fax->setFax_enabled_nighttime($faxDTO->getFax_enabled_nighttime());
        $fax->setFax_allow_dial_post($faxDTO->getFax_allow_dial_post());
        $fax->setFax_default_dialed_number($faxDTO->getFax_default_dialed_number());
        $fax->setFax_digits($faxDTO->getFax_digits());
        $fax->setFax_last_update_utc($faxDTO->getFax_last_update_utc());
        $fax->setBusiness_id($faxDTO->getBusiness_id());

        if (!$fax->getFax_allow_dial_post()) {
            $fax->setFax_digits("");
            $fax->setFax_default_dialed_number("");
        }

        return $this->faxDAO->guardar($fax);
    }

    public function eliminar($idEmpresa, $id)
    {
        $this->departamentoService = new DepartmentService();
        $this->personaService = new PersonaService();
        $this->parametroService = new ParametroService();
        $this->departamentoService->clearFaxDay($idEmpresa, $id);
        $this->departamentoService->clearFaxNight($idEmpresa, $id);
        $this->personaService->clearFaxDay($idEmpresa, $id);
        $this->personaService->clearFaxNight($idEmpresa, $id);
        $this->parametroService->clearFaxDay($idEmpresa, $id);
        $this->parametroService->clearFaxNight($idEmpresa, $id);
        
        $this->faxDAO->eliminar($idEmpresa, $id);
    }
}