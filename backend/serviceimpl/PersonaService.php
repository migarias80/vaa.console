<?php

namespace serviceimpl;

use daoimpl\DepartmentSQLDAO;
use daoimpl\FuncionSQLDAO;
use daoimpl\PersonaSQLDAO;
use daoimpl\PerTransferOptionSQLDAO;
use dto\DepTransferOptionDTO;
use dto\DepartmentDTO;
use dto\PersonaDTO;
use dto\PersonaMinDTO;
use dto\PerTransferOptionDTO;
use exception\DepartamentoRepetidoException;
use model\Department;
use model\DepTransferOption;
use model\Fax;
use model\Persona;
use model\PerTransferOption;
use model\TransferOption;
use service\IDepartmentService;
use service\IFaxService;
use daoimpl\DepTransferOptionSQLDAO;
use service\IPersonaService;
use \utils\LogUtils;

class PersonaService implements IPersonaService  {

    private $perTransferOptionDAO;
    private $phbDAO;
    private $funcionDAO;
    private $parametroService;

    function __construct() {
        $this->perTransferOptionDAO = new PerTransferOptionSQLDAO();
        $this->phbDAO = new PersonaSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
        $this->parametroService = new ParametroService();
    }

    public function getPersona($idEmpresa, $id=null, $id_dep=null, $incluirAcciones=false, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null)
    {
        $personas = $this->phbDAO->getPersona($idEmpresa, $id, $id_dep, null, $min, $faxIdDay, $faxIdNight, $VMIdDay, $VMIdNight);
        if ($personas == null) {
            return null;
        }
        $personasDTO = array();
        foreach ($personas as $persona) {
            if ($min) {
                $personaDTO = new PersonaMinDTO($persona);
            } else {
                $personaDTO = new PersonaDTO($persona);
                if ($incluirAcciones) {
                    $personaDTO->setTransferOptions($this->getPerTransferOption($personaDTO->getPhbId()));
                }
            }
            $personasDTO[] = $personaDTO;
        }

        if ($id != null) {
            if (count($personasDTO) == 1) {
                return $personasDTO[0];
            } else {
                return array();
            }
        }
        return $personasDTO;
    }

    public function getPersonaMinNoCast($idEmpresa)
    {
        $personas = $this->phbDAO->getPersonaMinNoCast($idEmpresa);
        if ($personas == null) {
            return null;
        }
        return $personas;
    }

    public function guardarPersona(PersonaDTO $personaDTO)
    {
        $persona = new Persona($personaDTO);
        $persona->setPhbFlags(40);
        $personaId = $this->phbDAO->guardar($persona);

        $this->eliminarDepTransferOption($personaId);
        foreach ($personaDTO->getTransferOptions() as $optionDTO) {
            $option = new PerTransferOption($optionDTO);
            $option->setTapPhbId($personaId);
            $this->perTransferOptionDAO->guardar($option);
        }

        if (!$personaDTO->getPhbIsSec()){
            $this->phbDAO->desvincularSecretaria($personaId);
        }

        return $personaId;
    }

    public function eliminarPersona($idEmpresa, $id)
    {
        $this->phbDAO->desvincularSecretaria($id);

        $this->eliminarDepTransferOption($id);

        $this->desvincularPersonaComoDestinoDeTransferencia($id);

        $this->phbDAO->eliminar($idEmpresa, $id);
    }

    public function getSecretaria($idEmpresa)
    {
        $personas = $this->phbDAO->getPersona($idEmpresa, null, null, true);
        if ($personas == null) {
            return null;
        }

        $personasDTO = array();
        foreach ($personas as $persona) {
            $personasDTO[] = new PersonaDTO($persona);
        }
        return $personasDTO;
    }

    public function getTransferibles($idEmpresa)
    {
        $personas = $this->phbDAO->getPersona($idEmpresa, null, null, null, false, null, null, null, null, true);
        if ($personas == null) {
            return null;
        }

        $personasDTO = array();
        foreach ($personas as $persona) {
            $personasDTO[] = new PersonaDTO($persona);
        }
        return $personasDTO;
    }

    public function getPerTransferOption($idPersona)
    {
        $perTransferOptions = $this->perTransferOptionDAO->getPerTransferOption($idPersona);
        if ($perTransferOptions == null) {
            return null;
        }

        $perTransferOptionsDTO = array();
        foreach ($perTransferOptions as $perTransferOption) {
            $perTransferOptionsDTO[] = new PerTransferOptionDTO($perTransferOption);
        }
        return $perTransferOptionsDTO;
    }

    public function eliminarDepTransferOption($idPersona)
    {
        $this->perTransferOptionDAO->eliminar($idPersona);
    }

    public function getCountPersonas($idEmpresa)
    {
        $countPersonas = $this->phbDAO->getCountPersonas($idEmpresa);
        if ($countPersonas == null) {
            return 0;
        }
        return $countPersonas;
    }

    public function clearFaxDay($idEmpresa, $idFax) {
        $this->phbDAO->clearFaxDay($idEmpresa, $idFax);
    }

    public function clearFaxNight($idEmpresa, $idFax) {
        $this->phbDAO->clearFaxNight($idEmpresa, $idFax);
    }

    public function clearVoiceMailDay($idEmpresa, $idVoiceMail) {
        $this->phbDAO->clearVoiceMailDay($idEmpresa, $idVoiceMail);
    }

    public function clearVoiceMailNight($idEmpresa, $idVoiceMail) {
        $this->phbDAO->clearVoiceMailNight($idEmpresa, $idVoiceMail);
    }

    public function desvincularPersonaComoDestinoDeTransferencia($idPersona)
    {
        $this->perTransferOptionDAO->desvincularPersonaComoDestinoDeTransferencia($idPersona);
    }

}