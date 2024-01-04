<?php

namespace serviceimpl;

use daoimpl\DepartmentSQLDAO;
use daoimpl\FuncionSQLDAO;
use dto\DepTransferOptionDTO;
use dto\DepartmentDTO;
use dto\DepartmentMinDTO;
use exception\DepartamentoRepetidoException;
use model\Department;
use model\DepTransferOption;
use model\Fax;
use model\TransferOption;
use service\IDepartmentService;
use service\IFaxService;
use daoimpl\DepTransferOptionSQLDAO;
use \utils\LogUtils;
use daoimpl\PersonaSQLDAO;
use \Exception;

class DepartmentService implements IDepartmentService {

    private $depTransferOptionDAO;
    private $depDAO;
    private $funcionDAO;
    private $parametroService;
    private $personaDAO;

    function __construct() {
        $this->depTransferOptionDAO = new DepTransferOptionSQLDAO();
        $this->depDAO = new DepartmentSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
        $this->parametroService = New ParametroService();
        $this->personaDAO = new PersonaSQLDAO();
    }

    public function getDepartment($idEmpresa, $id=null, $name=null, $incluirAcciones=false, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null)
    {
        $departments = $this->depDAO->getDepartment($idEmpresa, $id, $name, $min, $faxIdDay, $faxIdNight, $VMIdDay, $VMIdNight);
        if ($departments == null) {
            return null;
        }

        $departmentsDTO = array();
        foreach ($departments as $department) {
            if ($min) {
                $departamentoDTO = new DepartmentMinDTO($department);
            } else {
                $departamentoDTO = new DepartmentDTO($department);
                if ($incluirAcciones) {
                    $departamentoDTO->setTransferOptions($this->getDepTransferOption($departamentoDTO->getDepId()));
                }
            }
            $departmentsDTO[] = $departamentoDTO;
        }

        if ($id != null) {
            if (count($departmentsDTO) == 1) {
                return $departmentsDTO[0];
            } else {
                return array();
            }
        }
        return $departmentsDTO;
    }

    public function guardarDepartment(DepartmentDTO $departmentDTO)
    {
        // Validaciones
        $dptoBuscado = $this->getDepartment($departmentDTO->getBusinessId(), null, $departmentDTO->getDepName());
        if ($dptoBuscado != null && count($dptoBuscado)>0) {
            $existe = false;
            foreach ($dptoBuscado as $item) {
                if ($item->getDepId() != $departmentDTO->getDepId()) {
                    $existe = true;
                }
            }
            if ($existe) {
                LogUtils::INFO("Departamento ya existente", __CLASS__, __FUNCTION__, array($departmentDTO->getDepName()));
                throw new DepartamentoRepetidoException($departmentDTO->getDepName());
                return;
            }
        }

        // Guardado del departamento
        $department = new Department($departmentDTO);
        $depId = $this->depDAO->guardar($department);

        // Guardado de sus opciones de transferencia
        if ($depId) {
            $this->eliminarDepTransferOption($depId);
            foreach ($departmentDTO->getTransferOptions() as $optionDTO) {
                $option = new DepTransferOption($optionDTO);
                $option->setTadDepId($depId);

                $this->depTransferOptionDAO->guardar($option);
            }
        }

        return $depId;
    }

    public function eliminarDepartment($idEmpresa, $id)
    {
        $personasDelDpto = $this->personaDAO->getPersona($idEmpresa, null, $id);
        if (count($personasDelDpto) > 0) {
            // throw new Exception("No es posible eliminar el departamento ya que se posee personas asociadas");
            $this->depDAO->eliminarPersonasDelDepartamento($idEmpresa, $id);
        }

        $this->eliminarDepTransferOption($id);

        $this->depDAO->eliminar($idEmpresa, $id);
    }

    public function getDepTransferOption($idDepartment)
    {
        $depTransferOptions = $this->depTransferOptionDAO->getDepTransferOption($idDepartment);
        if ($depTransferOptions == null) {
            return null;
        }

        $depTransferOptionsDTO = array();
        foreach ($depTransferOptions as $depTransferOption) {
            $depTransferOptionsDTO[] = new DepTransferOptionDTO($depTransferOption);
        }
        return $depTransferOptionsDTO;
    }

    public function guardarDepTransferOption(DepTransferOptionDTO $depTransferOptionDTO)
    {
        $depTransferOption = new DepTransferOption();
        $depTransferOption->setTadDepId($depTransferOptionDTO->getTadDepId());
        $depTransferOption->setTadDepTransferType($depTransferOptionDTO->getTadDepTransferType());
        $depTransferOption->setTadTaoId($depTransferOptionDTO->getTadTaoId());
        $depTransferOption->setTadOriginNumber($depTransferOptionDTO->getTadOriginNumber());
        $depTransferOption->setTadBusy($depTransferOptionDTO->getTadBusy());
        $depTransferOption->setTadDaytime($depTransferOptionDTO->getTadDaytime());
        $depTransferOption->setTadIntGuide($depTransferOptionDTO->getTadIntGuide());
        $depTransferOption->setTadOrder($depTransferOptionDTO->getTadOrder());

        $this->depTransferOptionDAO->guardar($depTransferOption);
    }

    public function eliminarDepTransferOption($idDepartment)
    {
        $this->depTransferOptionDAO->eliminar($idDepartment);
    }

    public function getCountDepartments($idEmpresa)
    {
        $countDepartments = $this->depDAO->getCountDepartments($idEmpresa);
        if ($countDepartments == null) {
            return 0;
        }

        return $countDepartments;
    }

    public function clearFaxDay($idEmpresa, $idFax) {
        $this->depDAO->clearFaxDay($idEmpresa, $idFax);
    }

    public function clearFaxNight($idEmpresa, $idFax) {
        $this->depDAO->clearFaxNight($idEmpresa, $idFax);
    }

    public function clearVoiceMailDay($idEmpresa, $idVoiceMail) {
        $this->depDAO->clearVoiceMailDay($idEmpresa, $idVoiceMail);
    }

    public function clearVoiceMailNight($idEmpresa, $idVoiceMail) {
        $this->depDAO->clearVoiceMailNight($idEmpresa, $idVoiceMail);
    }

}