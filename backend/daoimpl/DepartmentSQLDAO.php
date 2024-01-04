<?php

namespace daoimpl;

use dao\FLAG_POS;
use dao\IDepartmentDAO;
use \dao\IParametroDAO;
use \db\SQLConnectPDO;
use \model\ConfirmationOption;
use model\Department;
use model\DepartmentMin;
use model\TransferOption;
use \dao\AGenericDAO;

class DepartmentSQLDAO extends AGenericDAO implements IDepartmentDAO
{

    public function getDepartment($idEmpresa, $id=null, $name=null, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",vaa_departments.DEP_COMPANY_ID COMPANY_ID ";

        // Query
        $sql = "
            SELECT 
                vaa_departments.*
                $selectEmpresa
            FROM vaa_departments
            WHERE 1 = 1
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        if ($id != null) {
            $sql .= "AND dep_id = :dep_id ";
        }
        if ($name != null) {
            $sql .= "AND dep_name = :dep_name ";
        }
        if ($faxIdDay != null) {
            $sql .= "AND dep_fax_daytime = :dep_fax_daytime ";
        }
        if ($faxIdNight != null) {
            $sql .= "AND dep_fax_nighttime = :dep_fax_nighttime ";
        }
        if ($VMIdDay != null) {
            $sql .= "AND dep_vma_daytime = :dep_vma_daytime ";
        }
        if ($VMIdNight != null) {
            $sql .= "AND dep_vma_nighttime = :dep_vma_nighttime ";
        }
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($id != null) {
            $stmt->bindParam(":dep_id", $id, \PDO::PARAM_INT);
        }
        if ($name != null) {
            $stmt->bindParam(":dep_name", $name, \PDO::PARAM_STR);
        }
        if ($faxIdDay != null) {
            $stmt->bindParam(":dep_fax_daytime", $faxIdDay, \PDO::PARAM_INT);
        }
        if ($faxIdNight != null) {
            $stmt->bindParam(":dep_fax_nighttime", $faxIdNight, \PDO::PARAM_INT);
        }
        if ($VMIdDay != null) {
            $stmt->bindParam(":dep_vma_daytime", $VMIdDay, \PDO::PARAM_INT);
        }
        if ($VMIdNight != null) {
            $stmt->bindParam(":dep_vma_nighttime", $VMIdNight, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $deps = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $dep) {
                if ($min) {
                    $deps[] = new DepartmentMin($dep);
                } else {
                    $dep['DEP_GE_ALLOW_PLAYBACK_INT_NUMBER'] = $this->StringToBoolean($dep['DEP_GE_ALLOW_PLAYBACK_INT_NUMBER'], false);
                    $dep['DEP_GI_ALLOW_PLAYBACK_INT_NUMBER'] = $this->StringToBoolean($dep['DEP_GI_ALLOW_PLAYBACK_INT_NUMBER'], false);
                    $dep['DEP_ALLOW_HTDF_EXT'] = $this->FlagDecode($dep['DEP_FLAGS'], FLAG_POS::ALLOW_HTDF_EXT);
                    $dep['DEP_ALLOW_HTDF_INT'] = $this->FlagDecode($dep['DEP_FLAGS'], FLAG_POS::ALLOW_HTDF_INT);
                    $dep['DEP_ALLOW_VOZ_EXT'] = $this->FlagDecode($dep['DEP_FLAGS'], FLAG_POS::ALLOW_VOZ_EXT);
                    $dep['DEP_ALLOW_VOZ_INT'] = $this->FlagDecode($dep['DEP_FLAGS'], FLAG_POS::ALLOW_VOZ_INT);
                    $dep['DEP_PLAY_MSG_INFO_TRANF'] = $this->FlagDecode($dep['DEP_FLAGS'], FLAG_POS::PLAY_MSG_INFO_TRANF);
                    $deps[] = new Department($dep);
                }
            }
        }

        return $deps;
    }

    public function guardar(Department $department)
    {
        $department->setDepGeAllowPlaybackIntNumber($this->BooleanToString($department->getDepGeAllowPlaybackIntNumber(), false));
        $department->setDepGiAllowPlaybackIntNumber($this->BooleanToString($department->getDepGiAllowPlaybackIntNumber(), false));
        $flagValue = $this->FlagEnconde(0, $department->getDepAllowHtdfExt(), FLAG_POS::ALLOW_HTDF_EXT);
        $flagValue = $this->FlagEnconde($flagValue, $department->getDepAllowHtdfInt(), FLAG_POS::ALLOW_HTDF_INT);
        $flagValue = $this->FlagEnconde($flagValue, $department->getDepAllowVozExt(), FLAG_POS::ALLOW_VOZ_EXT);
        $flagValue = $this->FlagEnconde($flagValue, $department->getDepAllowVozInt(), FLAG_POS::ALLOW_VOZ_INT);
        $flagValue = $this->FlagEnconde($flagValue, $department->getDepPlayMsgInfoTranf(), FLAG_POS::PLAY_MSG_INFO_TRANF);
        $department->setDepFlags($flagValue);

        $db = SQLConnectPDO::GetConnection();
        if ($department->getDepId() == null) {
            // Query
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                INSERT INTO vaa_departments
                (DEP_COMPANY_ID, dep_last_update_utc, dep_name, dep_email, dep_flags, dep_fax_daytime, dep_fax_nighttime, dep_confirmation, dep_daytime_number, dep_nighttime_number, dep_daytime_cellular, dep_nighttime_cellular, dep_ext_guide_number, dep_int_guide_number, dep_gi_allow_playback_int_number, dep_ge_allow_playback_int_number, dep_dialpost_number_fax_daytime, dep_dialpost_number_fax_nighttime, dep_vma_daytime, dep_vma_nighttime, dep_dialpost_number_vma_daytime, dep_dialpost_number_vma_nighttime, dep_fon_name)
                VALUES
                (:COMPANY_ID, getdate(), :dep_name, :dep_email, :dep_flags, :dep_fax_daytime, :dep_fax_nighttime, :dep_confirmation, :dep_daytime_number, :dep_nighttime_number, :dep_daytime_cellular, :dep_nighttime_cellular, :dep_ext_guide_number, :dep_int_guide_number, :dep_gi_allow_playback_int_number, :dep_ge_allow_playback_int_number, :dep_dialpost_number_fax_daytime, :dep_dialpost_number_fax_nighttime, :dep_vma_daytime, :dep_vma_nighttime, :dep_dialpost_number_vma_daytime, :dep_dialpost_number_vma_nighttime, :dep_fon_name)
            ");
            $stmt->bindValue(":COMPANY_ID", $department->getBusinessId(), \PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare(
                "SET NOCOUNT ON;
                UPDATE vaa_departments Set
                dep_last_update_utc = getdate(), 
                dep_name = :dep_name, 
                dep_email = :dep_email, 
                dep_flags = :dep_flags, 
                dep_fax_daytime = :dep_fax_daytime, 
                dep_fax_nighttime = :dep_fax_nighttime, 
                dep_confirmation = :dep_confirmation, 
                dep_daytime_number = :dep_daytime_number, 
                dep_nighttime_number = :dep_nighttime_number, 
                dep_daytime_cellular = :dep_daytime_cellular, 
                dep_nighttime_cellular = :dep_nighttime_cellular, 
                dep_ext_guide_number = :dep_ext_guide_number, 
                dep_int_guide_number = :dep_int_guide_number, 
                dep_gi_allow_playback_int_number = :dep_gi_allow_playback_int_number, 
                dep_ge_allow_playback_int_number = :dep_ge_allow_playback_int_number, 
                dep_dialpost_number_fax_daytime = :dep_dialpost_number_fax_daytime, 
                dep_dialpost_number_fax_nighttime = :dep_dialpost_number_fax_nighttime, 
                dep_vma_daytime = :dep_vma_daytime, 
                dep_vma_nighttime = :dep_vma_nighttime, 
                dep_dialpost_number_vma_daytime = :dep_dialpost_number_vma_daytime, 
                dep_dialpost_number_vma_nighttime = :dep_dialpost_number_vma_nighttime, 
                dep_fon_name = :dep_fon_name
                WHERE vaa_departments.dep_id = :dep_id "
            );
            $stmt->bindValue(":dep_id", $department->getDepId(), \PDO::PARAM_INT);
        }
        $stmt->bindValue(":dep_name", $department->getDepName(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_email", $department->getDepEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_fon_name", $department->getDepFonName(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_flags", $department->getDepFlags(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_confirmation", $department->getDepConfirmation(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_daytime_number", $department->getDepDaytimeNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_nighttime_number", $department->getDepNighttimeNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_daytime_cellular", $department->getDepDaytimeCellular(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_nighttime_cellular", $department->getDepNighttimeCellular(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_ext_guide_number", $department->getDepExtGuideNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_int_guide_number", $department->getDepIntGuideNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_gi_allow_playback_int_number", $department->getDepGiAllowPlaybackIntNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_ge_allow_playback_int_number", $department->getDepGeAllowPlaybackIntNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_fax_daytime", $department->getDepFaxDaytime(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_fax_nighttime", $department->getDepFaxNighttime(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_dialpost_number_fax_daytime", $department->getDepDialpostNumberFaxDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_dialpost_number_fax_nighttime", $department->getDepDialpostNumberFaxNighttime(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_vma_daytime", $department->getDepVmaDaytime(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_vma_nighttime", $department->getDepVmaNighttime(), \PDO::PARAM_INT);
        $stmt->bindValue(":dep_dialpost_number_vma_daytime", $department->getDepDialpostNumberVmaDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":dep_dialpost_number_vma_nighttime", $department->getDepDialpostNumberVmaNighttime(), \PDO::PARAM_STR);

        $stmt->execute();

        if ($department->getDepId() == null) {
            return $db->lastInsertId();
        } else {
            return $department->getDepId();
        }
    }

    public function eliminar($idEmpresa, $id)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE From vaa_departments 
            WHERE dep_id = :dep_id 
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":dep_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function eliminarPersonasDelDepartamento($idEmpresa, $id)
    {
        // Se pasan a null los asistentes que se esten usando
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_phone_book SET
                phb_sec_id = null
            WHERE phb_sec_id IN 
                (SELECT phb_id 
                FROM vaa_phone_book
                WHERE phb_dep_id = :dep_id)
            AND phb_dep_id = 
                (SELECT 
                    vaa_departments.dep_id 
                FROM vaa_departments 
                WHERE 
                    vaa_departments.dep_id = vaa_phone_book.phb_dep_id 
                    AND vaa_departments.DEP_COMPANY_ID = :COMPANY_ID)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":dep_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        // Se pasan a null las personas encontradas en opciones de transferencia
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_transfer_action_phb SET
                tap_transf_phb_id = null
            WHERE tap_transf_phb_id IN 
                (SELECT phb_id 
                FROM vaa_phone_book
                WHERE phb_dep_id = :dep_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":dep_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        // Se eliminan las opciones de transferencia de las personas que se van a quitar
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            SET NOCOUNT ON;
                DELETE FROM vaa_transfer_action_phb 
                WHERE tap_phb_id IN
                    (SELECT phb_id 
                    FROM vaa_phone_book
                    WHERE phb_dep_id = :dep_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":dep_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        // Se eliminan las personas del departamento
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_phone_book 
            WHERE phb_dep_id = :dep_id 
            AND vaa_phone_book.phb_dep_id = 
                (SELECT 
                    vaa_departments.dep_id 
                FROM vaa_departments 
                WHERE 
                    vaa_departments.dep_id = vaa_phone_book.phb_dep_id 
                    AND vaa_departments.DEP_COMPANY_ID = :COMPANY_ID
                )";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":dep_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCountDepartments($idEmpresa)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SELECT 
              COUNT(vaa_departments.dep_id)
              AS CANTIDAD
            FROM vaa_departments
            WHERE 1 = 1
        ";

        // Filters
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Params
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();

        $deps = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $dep) {
                return $dep['CANTIDAD'];
            }
        }

        return 0;
    }

    public function clearFaxDay($idEmpresa, $idFax)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_departments 
            SET dep_fax_daytime = null
            WHERE dep_fax_daytime = :fax_id
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":fax_id", $idFax, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearFaxNight($idEmpresa, $idFax)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_departments 
            SET dep_fax_nighttime = null
            WHERE dep_fax_nighttime = :fax_id
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":fax_id", $idFax, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearVoiceMailDay($idEmpresa, $idVoiceMail)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_departments 
            SET dep_vma_daytime = null
            WHERE dep_vma_daytime = :vma_id
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":vma_id", $idVoiceMail, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearVoiceMailNight($idEmpresa, $idVoiceMail)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_departments 
            SET dep_vma_nighttime = null
            WHERE dep_vma_nighttime = :vma_id
        ";

        // Filtros
        $sql .= "AND DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":vma_id", $idVoiceMail, \PDO::PARAM_INT);
        $stmt->execute();
    }
    
}