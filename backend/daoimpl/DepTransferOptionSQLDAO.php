<?php

namespace daoimpl;

use dao\IDepTransferOptionDAO;
use \db\SQLConnectPDO;
use \model\DepTransferOption;
use \dao\AGenericDAO;

class DepTransferOptionSQLDAO extends AGenericDAO implements IDepTransferOptionDAO {

    public function getDepTransferOption($idDepartment)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query        
        $sql = "
            SELECT * 
            FROM vaa_transfer_action_dep
            WHERE 1 = 1
        ";

        $sql .= "AND tad_dep_id = :tad_dep_id ";

        $stmt = $db->prepare($sql);

        if ($idDepartment != null) {
            $stmt->bindParam(":tad_dep_id", $idDepartment, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $tads = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $tad) {
                $tad['TAD_INT_GUIDE'] = $this->StringToBoolean($tad['TAD_INT_GUIDE']);
                $tad['TAD_DAYTIME'] = $this->StringToBoolean($tad['TAD_DAYTIME']);
                $tad['TAD_BUSY'] = $this->StringToBoolean($tad['TAD_BUSY']);
                $tads[] = new DepTransferOption($tad);
            }
        }

        return $tads;
    }

    public function guardar(DepTransferOption $tad)
    {
        if ($tad->getTadDepTransferType() == 1) {
            $tad->setTadDepTransferType("C");
        } elseif ($tad->getTadDepTransferType() == 2) {
            $tad->setTadDepTransferType("P");
        }

        $tad->setTadIntGuide($this->BooleanToString($tad->getTadIntGuide()));
        $tad->setTadDaytime($this->BooleanToString($tad->getTadDaytime()));
        $tad->setTadBusy($this->BooleanToString($tad->getTadBusy()));
        if  ($tad->getTadOriginNumber() == "") {
            $tad->setTadOriginNumber("DEFAULT");
        }

        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            INSERT INTO vaa_transfer_action_dep
            (tad_dep_id,
            tad_int_guide,
            tad_daytime,
            tad_order,
            tad_busy,
            tad_origin_number,
            tad_tao_id,
            tad_dep_transfer_type)
            VALUES
            (:tad_dep_id,
            :tad_int_guide,
            :tad_daytime,
            :tad_order,
            :tad_busy,
            :tad_origin_number,
            :tad_tao_id,
            :tad_dep_transfer_type) 
        ");

        $stmt->bindValue(":tad_dep_id", $tad->getTadDepId(), \PDO::PARAM_INT);
        $stmt->bindValue(":tad_int_guide", $tad->getTadIntGuide(), \PDO::PARAM_STR);
        $stmt->bindValue(":tad_daytime", $tad->getTadDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":tad_order", $tad->getTadOrder(), \PDO::PARAM_INT);
        $stmt->bindValue(":tad_busy", $tad->getTadBusy(), \PDO::PARAM_STR);
        $stmt->bindValue(":tad_origin_number", $tad->getTadOriginNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":tad_tao_id", $tad->getTadTaoId(), \PDO::PARAM_INT);
        $stmt->bindValue(":tad_dep_transfer_type", $tad->getTadDepTransferType(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function eliminar($idDepartment)
    {
        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            DELETE FROM vaa_transfer_action_dep 
            WHERE tad_dep_id = :tad_dep_id 
        ");
        $stmt->bindValue(":tad_dep_id", $idDepartment, \PDO::PARAM_INT);

        $stmt->execute();
    }

}
