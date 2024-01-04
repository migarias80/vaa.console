<?php

namespace daoimpl;

use \dao\IDomainDAO;
use \db\SQLConnectPDO;
use \model\Domain;
use \dao\AGenericDAO;

class DomainSQLDAO extends AGenericDAO implements IDomainDAO {

    public function getDomain($idEmpresa, $id=null, $regex=null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",DPL_COMPANY_ID COMPANY_ID ";

        // Query
        $sql = "
            SELECT 
                vaa2_dial_plan.*
                $selectEmpresa
            FROM vaa2_dial_plan
            WHERE 1 = 1
        ";

        // Filtros
        $sql .= "AND DPL_COMPANY_ID = :COMPANY_ID ";
        if ($id != null) {
            $sql .= "AND dpl_id = :dpl_id ";
        }
        if ($regex != null) {
            $sql .= "AND dpl_regex = :dpl_regex ";
        }
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($id != null) {
            $stmt->bindParam(":dpl_id", $id, \PDO::PARAM_INT);
        }
        if ($regex != null) {
            $stmt->bindParam(":dpl_regex", $regex, \PDO::PARAM_STR);
        }

        $sql .= "ORDER BY vaa2_dial_plan.dpl_id ASC ";

        $stmt->execute();

        $domains = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $domain) {
                $domains[] = new Domain($domain);
            }
        }

        return $domains;
    }

    public function guardar(Domain $domain)
    {
        $db = SQLConnectPDO::GetConnection();
        if ($domain->getDom_id() == null) {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                INSERT INTO vaa2_dial_plan
                (dpl_regex, dpl_ip_domain, dpl_use_ani_ip_for_refer, DPL_COMPANY_ID)
                VALUES
                (:dpl_regex, :dpl_ip_domain, :dpl_use_ani_ip_for_refer, :COMPANY_ID) 
            ");
            $stmt->bindValue(":COMPANY_ID", $domain->getBusiness_id(), \PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare(
                "SET NOCOUNT ON;
                UPDATE vaa2_dial_plan SET
                dpl_regex = :dpl_regex,
                dpl_ip_domain = :dpl_ip_domain,
                dpl_use_ani_ip_for_refer = :dpl_use_ani_ip_for_refer
                WHERE vaa2_dial_plan.dpl_id = :dpl_id "
            );
            $stmt->bindValue(":dpl_id", $domain->getDom_id(), \PDO::PARAM_INT);
        }

        $stmt->bindValue(":dpl_regex", $domain->getDom_regex(), \PDO::PARAM_STR);
        $stmt->bindValue(":dpl_ip_domain", $domain->getDom_domain(), \PDO::PARAM_STR);
        $stmt->bindValue(":dpl_use_ani_ip_for_refer", $domain->getDom_use_ani_ip_for_refer(), \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function eliminar($idEmpresa, $id)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa2_dial_plan 
            WHERE dpl_id = :dpl_id
        ";

        // Filtros
        $sql .= "AND DPL_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":dpl_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

}
