<?php

namespace daoimpl;

use \dao\IFaxDAO;
use \db\SQLConnectPDO;
use \model\Fax;
use \dao\AGenericDAO;

class FaxSQLDAO extends AGenericDAO implements IFaxDAO {

    public function getFax($idEmpresa, $id=null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SELECT 
                vaa_faxes.*,
                FAX_COMPANY_ID COMPANY_ID
            FROM vaa_faxes
            WHERE 1 = 1
        ";

        // Filtros
        $sql .= "AND FAX_COMPANY_ID = :COMPANY_ID ";
        if ($id != null) {
            $sql .= "AND fax_id = :fax_id ";
        }
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($id != null) {
            $stmt->bindParam(":fax_id", $id, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $faxes = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $fax) {
                $fax['FAX_ENABLED_DAYTIME'] = $this->StringToBoolean($fax['FAX_ENABLED_DAYTIME']);
                $fax['FAX_ENABLED_NIGHTTIME'] = $this->StringToBoolean($fax['FAX_ENABLED_NIGHTTIME']);
                $fax['FAX_ALLOW_DIAL_POST'] = $this->StringToBoolean($fax['FAX_ALLOW_DIAL_POST']);
                $faxes[] = new Fax($fax);
            }
        }

        return $faxes;
    }

    public function guardar(Fax $fax)
    {
        $fax->setFax_allow_dial_post($this->BooleanToString($fax->getFax_allow_dial_post()));
        $fax->setFax_enabled_daytime($this->BooleanToString($fax->getFax_enabled_daytime()));
        $fax->setFax_enabled_nighttime($this->BooleanToString($fax->getFax_enabled_nighttime()));

        $db = SQLConnectPDO::GetConnection();
        if ($fax->getFax_id() == null) {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                INSERT INTO vaa_faxes
                (fax_description, fax_internal_number, fax_enabled_daytime, fax_enabled_nighttime, fax_allow_dial_post, fax_default_dialed_number, fax_digits, fax_last_update_utc, FAX_COMPANY_ID)
                VALUES
                (:fax_description, :fax_internal_number, :fax_enabled_daytime, :fax_enabled_nighttime, :fax_allow_dial_post, :fax_default_dialed_number, :fax_digits, getdate(), :COMPANY_ID) 
            ");
            $stmt->bindValue(":COMPANY_ID", $fax->getBusiness_id(), \PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare(
                "SET NOCOUNT ON;
                UPDATE vaa_faxes SET
                fax_description = :fax_description,
                fax_internal_number = :fax_internal_number,
                fax_enabled_daytime = :fax_enabled_daytime,
                fax_enabled_nighttime = :fax_enabled_nighttime,
                fax_allow_dial_post = :fax_allow_dial_post,
                fax_default_dialed_number = :fax_default_dialed_number,
                fax_digits = :fax_digits,
                fax_last_update_utc = getdate()
                WHERE vaa_faxes.fax_id = :fax_id "
            );
            $stmt->bindValue(":fax_id", $fax->getFax_id(), \PDO::PARAM_INT);
        }

        $stmt->bindValue(":fax_description", $fax->getFax_description(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_internal_number", $fax->getFax_internal_number(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_enabled_daytime", $fax->getFax_enabled_daytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_enabled_nighttime", $fax->getFax_enabled_nighttime(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_allow_dial_post", $fax->getFax_allow_dial_post(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_default_dialed_number", $fax->getFax_default_dialed_number(), \PDO::PARAM_STR);
        $stmt->bindValue(":fax_digits", $fax->getFax_digits(), \PDO::PARAM_STR);
        $stmt->execute();

        if ($fax->getFax_id() == null) {
            return $db->lastInsertId();
        }
        return $fax->getFax_id();
    }

    public function eliminar($idEmpresa, $id)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_faxes 
            WHERE fax_id = :fax_id
        ";

        // Filtros
        // if (CONSOLE_MODE != "MONO") {
        $sql .= "AND FAX_COMPANY_ID = :COMPANY_ID ";
        // }
        $stmt = $db->prepare($sql);

        // Parametros
        // if (CONSOLE_MODE != "MONO") {
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        // }
        $stmt->bindValue(":fax_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

}
