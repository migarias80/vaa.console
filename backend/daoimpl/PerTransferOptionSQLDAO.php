<?php

namespace daoimpl;

use dao\IPerTransferOptionDAO;
use \db\SQLConnectPDO;
use \dao\AGenericDAO;
use model\PerTransferOption;

class PerTransferOptionSQLDAO extends AGenericDAO implements IPerTransferOptionDAO  {

    public function getPerTransferOption($idPersona)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT * 
            FROM vaa_transfer_action_phb
            WHERE 1 = 1
        ";

        $sql .= "AND tap_phb_id = :tap_phb_id ";

        // Ordenamiento fundamental para visualizacion de datos
        $sql .= "ORDER BY tap_int_guide DESC, tap_daytime DESC, tap_phb_transfer_type ASC, tap_origin_number ASC, tap_order ASC ";

        $stmt = $db->prepare($sql);

        if ($idPersona != null) {
            $stmt->bindParam(":tap_phb_id", $idPersona, \PDO::PARAM_INT);
        }

        $stmt->execute();

        $taps = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $tap) {
                $tap['TAP_INT_GUIDE'] = $this->StringToBoolean($tap['TAP_INT_GUIDE']);
                $tap['TAP_DAYTIME'] = $this->StringToBoolean($tap['TAP_DAYTIME']);
                $tap['TAP_BUSY'] = $this->StringToBoolean($tap['TAP_BUSY']);
                $taps[] = new PerTransferOption($tap);
            }
        }

        return $taps;
    }

    public function guardar(PerTransferOption $tap)
    {
        if ($tap->getTapPhbTransferType() == 1) {
            $tap->setTapPhbTransferType("C");
        } elseif ($tap->getTapPhbTransferType() == 2) {
            $tap->setTapPhbTransferType("P");
        } elseif ($tap->getTapPhbTransferType() == 3) {
            $tap->setTapPhbTransferType("S");
        }

        $tap->setTapIntGuide($this->BooleanToString($tap->getTapIntGuide()));
        $tap->setTapDaytime($this->BooleanToString($tap->getTapDaytime()));
        $tap->setTapBusy($this->BooleanToString($tap->getTapBusy()));
        if  ($tap->getTapOriginNumber() == "") {
            $tap->setTapOriginNumber("DEFAULT");
        }

        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            INSERT INTO vaa_transfer_action_phb
            (tap_phb_id,
            tap_int_guide,
            tap_daytime,
            tap_order,
            tap_busy,
            tap_origin_number,
            tap_tao_id,
            tap_phb_transfer_type,
            tap_transf_phb_id)
            VALUES
            (:tap_phb_id,
            :tap_int_guide,
            :tap_daytime,
            :tap_order,
            :tap_busy,
            :tap_origin_number,
            :tap_tao_id,
            :tap_phb_transfer_type,
            :tap_transf_phb_id) 
        ");

        $stmt->bindValue(":tap_phb_id", $tap->getTapPhbId(), \PDO::PARAM_INT);
        $stmt->bindValue(":tap_int_guide", $tap->getTapIntGuide(), \PDO::PARAM_STR);
        $stmt->bindValue(":tap_daytime", $tap->getTapDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":tap_order", $tap->getTapOrder(), \PDO::PARAM_INT);
        $stmt->bindValue(":tap_busy", $tap->getTapBusy(), \PDO::PARAM_STR);
        $stmt->bindValue(":tap_origin_number", $tap->getTapOriginNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":tap_tao_id", $tap->getTapTaoId(), \PDO::PARAM_INT);
        $stmt->bindValue(":tap_phb_transfer_type", $tap->getTapPhbTransferType(), \PDO::PARAM_STR);
        $stmt->bindValue(":tap_transf_phb_id", $tap->getTapTransfPhbId(), \PDO::PARAM_STR);

        $stmt->execute();
    }

    public function eliminar($idPersona)
    {
        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            DELETE FROM vaa_transfer_action_phb 
            WHERE tap_phb_id = :tap_phb_id 
        ");
        $stmt->bindValue(":tap_phb_id", $idPersona, \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function desvincularPersonaComoDestinoDeTransferencia($idPersona) {
        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa_transfer_action_phb SET
                tap_transf_phb_id = null
            WHERE tap_transf_phb_id = :tap_transf_phb_id 
        ");
        $stmt->bindValue(":tap_transf_phb_id", $idPersona, \PDO::PARAM_INT);

        $stmt->execute();
    }

}
