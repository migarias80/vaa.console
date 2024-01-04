<?php

namespace serviceimpl;

use daoimpl\FuncionSQLDAO;
use dto\VoiceMailDTO;
use model\VoiceMail;
use service\IVoiceMailService;
use daoimpl\VoiceMailSQLDAO;
use serviceimpl\DepartmentService;
use serviceimpl\PersonaService;
use \Exception;

class VoiceMailService implements IVoiceMailService
{

    private $voiceMailDAO;
    private $funcionDAO;

    function __construct() {
        $this->voiceMailDAO = new VoiceMailSQLDAO();
        $this->funcionDAO = new FuncionSQLDAO();
    }

    function getVoiceMail($idEmpresa, $id=null) {
        $voiceMails = $this->voiceMailDAO->getVoiceMail($idEmpresa, $id);
        if ($voiceMails == null) {
            return null;
        }

        $voiceMailsDTO = array();
        foreach ($voiceMails as $voiceMail) {
            $voiceMailsDTO[] = new VoiceMailDTO($voiceMail);
        }
        return $voiceMailsDTO;
    }

    function guardar(VoiceMailDTO $voiceMailDTO) {
        if ($voiceMailDTO->getVma_id() != null) {
            $this->departamentoService = new DepartmentService();
            $this->personaService = new PersonaService();
            $departamentosConElVM_Day = $this->departamentoService->getDepartment($voiceMailDTO->getBusiness_id(), null, null, false, true, null, null, $voiceMailDTO->getVma_id());
            $departamentosConElVM_Night = $this->departamentoService->getDepartment($voiceMailDTO->getBusiness_id(), null, null, false, true, null, null, null, $voiceMailDTO->getVma_id());
            $personasConElVM_Day = $this->personaService->getPersona($voiceMailDTO->getBusiness_id(), null, null, false, true, null, null, $voiceMailDTO->getVma_id());
            $personasConElVM_Night = $this->personaService->getPersona($voiceMailDTO->getBusiness_id(), null, null, false, true, null, null, null, $voiceMailDTO->getVma_id());
            if (!$voiceMailDTO->getVma_enabled_daytime() && count($departamentosConElVM_Day) > 0) {
                throw new Exception("No es posible actualizar el voice mail ya que se encuentra configurado como diurno en al menos un departamento");
            }
            if (!$voiceMailDTO->getVma_enabled_nighttime() && count($departamentosConElVM_Night) > 0) {
                throw new Exception("No es posible actualizar el voice mail ya que se encuentra configurado como nocturno en al menos un departamento");
            }
            if (!$voiceMailDTO->getVma_enabled_daytime() && count($personasConElVM_Day) > 0) {
                throw new Exception("No es posible actualizar el voice mail ya que se encuentra configurado como diurno en al menos una persona");
            }
            if (!$voiceMailDTO->getVma_enabled_nighttime() && count($personasConElVM_Night) > 0) {
                throw new Exception("No es posible actualizar el voice mail ya que se encuentra configurado como nocturno en al menos una persona");
            }
        }

        $voiceMail = new VoiceMail();
        $voiceMail->setVma_id($voiceMailDTO->getVma_id());
        $voiceMail->setVma_description($voiceMailDTO->getVma_description());
        $voiceMail->setVma_internal_number($voiceMailDTO->getVma_internal_number());
        $voiceMail->setVma_enabled_daytime($voiceMailDTO->getVma_enabled_daytime());
        $voiceMail->setVma_enabled_nighttime($voiceMailDTO->getVma_enabled_nighttime());
        $voiceMail->setVma_allow_dial_post($voiceMailDTO->getVma_allow_dial_post());
        $voiceMail->setVma_default_dialed_number($voiceMailDTO->getVma_default_dialed_number());
        $voiceMail->setVma_digits($voiceMailDTO->getVma_digits());
        $voiceMail->setVma_last_update_utc($voiceMailDTO->getVma_last_update_utc());
        $voiceMail->setBusiness_id($voiceMailDTO->getBusiness_id());

        if (!$voiceMail->getVma_allow_dial_post()) {
            $voiceMail->setVma_digits("");
            $voiceMail->setVma_default_dialed_number("");
        }

        return $this->voiceMailDAO->guardar($voiceMail);
    }

    public function eliminar($idEmpresa, $id)
    {
        $this->departamentoService = new DepartmentService();
        $this->personaService = new PersonaService();
        $this->parametroService = new ParametroService();
        $this->departamentoService->clearVoiceMailDay($idEmpresa, $id);
        $this->departamentoService->clearVoiceMailNight($idEmpresa, $id);
        $this->personaService->clearVoiceMailDay($idEmpresa, $id);
        $this->personaService->clearVoiceMailNight($idEmpresa, $id);
        $this->parametroService->clearVoiceMailDay($idEmpresa, $id);
        $this->parametroService->clearVoiceMailNight($idEmpresa, $id);
        
        $this->voiceMailDAO->eliminar($idEmpresa, $id);
    }
}