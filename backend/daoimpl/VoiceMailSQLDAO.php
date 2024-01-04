<?php

namespace daoimpl;

use dao\IVoiceMailDAO;
use \db\SQLConnectPDO;
use \model\VoiceMail;
use \dao\AGenericDAO;

class VoiceMailSQLDAO extends AGenericDAO implements IVoiceMailDAO  {

    public function getVoiceMail($idEmpresa, $id = null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SELECT 
                vaa_voice_mails.*,
                VMA_COMPANY_ID COMPANY_ID 
            FROM vaa_voice_mails 
            WHERE 1 = 1
        ";

        // Filtros
        $sql .= "And VMA_COMPANY_ID = :COMPANY_ID ";
        if ($id != null) {
            $sql .= "And vma_id = :vma_id ";
        }
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($id != null) {
            $stmt->bindParam(":vma_id", $id, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $voiceMails = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $voiceMail) {
                $voiceMail['VMA_ENABLED_DAYTIME'] = $this->StringToBoolean($voiceMail['VMA_ENABLED_DAYTIME']);
                $voiceMail['VMA_ENABLED_NIGHTTIME'] = $this->StringToBoolean($voiceMail['VMA_ENABLED_NIGHTTIME']);
                $voiceMail['VMA_ALLOW_DIAL_POST'] = $this->StringToBoolean($voiceMail['VMA_ALLOW_DIAL_POST']);
                $voiceMails[] = new VoiceMail($voiceMail);
            }
        }

        return $voiceMails;
    }

    public function guardar(VoiceMail $voiceMail)
    {
        $voiceMail->setVma_allow_dial_post($this->BooleanToString($voiceMail->getVma_allow_dial_post()));
        $voiceMail->setVma_enabled_daytime($this->BooleanToString($voiceMail->getVma_enabled_daytime()));
        $voiceMail->setVma_enabled_nighttime($this->BooleanToString($voiceMail->getVma_enabled_nighttime()));

        $db = SQLConnectPDO::GetConnection();

        // Query
        if ($voiceMail->getVma_id() == null) {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                INSERT INTO vaa_voice_mails
                (vma_description, vma_internal_number, vma_enabled_daytime, vma_enabled_nighttime, vma_allow_dial_post, vma_default_dialed_number, vma_digits, vma_last_update_utc, VMA_COMPANY_ID) 
                VALUES 
                (:vma_description, :vma_internal_number, :vma_enabled_daytime, :vma_enabled_nighttime, :vma_allow_dial_post, :vma_default_dialed_number, :vma_digits, getdate(), :COMPANY_ID) 
            ");
            $stmt->bindValue(":COMPANY_ID", $voiceMail->getBusiness_id(), \PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare(
                "SET NOCOUNT ON;
                UPDATE vaa_voice_mails SET
                    vma_description = :vma_description,
                    vma_internal_number = :vma_internal_number,
                    vma_enabled_daytime = :vma_enabled_daytime,
                    vma_enabled_nighttime = :vma_enabled_nighttime,
                    vma_allow_dial_post = :vma_allow_dial_post,
                    vma_default_dialed_number = :vma_default_dialed_number,
                    vma_digits = :vma_digits,
                    vma_last_update_utc = getdate()
                WHERE vma_id = :vma_id "
            );
            $stmt->bindValue(":vma_id", $voiceMail->getVma_id(), \PDO::PARAM_INT);
        }

        $stmt->bindValue(":vma_description", $voiceMail->getVma_description(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_internal_number", $voiceMail->getVma_internal_number(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_enabled_daytime", $voiceMail->getVma_enabled_daytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_enabled_nighttime", $voiceMail->getVma_enabled_nighttime(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_allow_dial_post", $voiceMail->getVma_allow_dial_post(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_default_dialed_number", $voiceMail->getVma_default_dialed_number(), \PDO::PARAM_STR);
        $stmt->bindValue(":vma_digits", $voiceMail->getVma_digits(), \PDO::PARAM_STR);
        $stmt->execute();

        if ($voiceMail->getVma_id() == null) {
            return $db->lastInsertId();
        }
        return $voiceMail->getVma_id();
    }

    public function eliminar($idEmpresa, $id)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_voice_mails 
            WHERE vma_id = :vma_id 
        ";

        // Filtros
        $sql .= "And VMA_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":vma_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}