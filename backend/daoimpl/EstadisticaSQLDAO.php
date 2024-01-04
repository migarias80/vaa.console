<?php

namespace daoimpl;

use \db\SQLConnectPDO;
use \dao\IEstadisticaDAO;
use \dao\AGenericDAO;
use \model\Estadistica;

class EstadisticaSQLDAO extends AGenericDAO implements IEstadisticaDAO {

    public function getLlamadas($idEmpresa, $fechaDesde, $fechaHasta)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SELECT 
                Convert(varchar, vaa_daily_records.DAR_DATE, 103) DAR_DATE,
                vaa_daily_records.DAR_DATE DAR_DATE_VALUE,
                vaa_fields_daily_records.FDR_ID,
                vaa_fields_daily_records.FDR_DESCRIPTION,
                vaa_daily_records.DAR_VALUE,
                vaa_fields_daily_records.FDR_UNIT_SINGULAR,
                vaa_fields_daily_records.FDR_UNIT_PLURAL,
                (CASE WHEN vaa_daily_records.DAR_VALUE = 1 THEN vaa_fields_daily_records.FDR_UNIT_SINGULAR ELSE vaa_fields_daily_records.FDR_UNIT_PLURAL END) FDR_UNIT
            FROM vaa_daily_records
            INNER JOIN vaa_fields_daily_records ON vaa_fields_daily_records.fdr_id = vaa_daily_records.dar_fdr_id
            WHERE 1 = 1
            AND DAR_DATE BETWEEN :FECHA_DESDE AND :FECHA_HASTA
            AND vaa_fields_daily_records.FDR_ID > 999
        ";

        // Filtros
        $sql .= "AND DAR_COMPANY_ID = :COMPANY_ID ";
        $sql .= "ORDER BY vaa_daily_records.DAR_DATE ASC, vaa_daily_records.DAR_FDR_ID ASC ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":FECHA_DESDE", $fechaDesde, \PDO::PARAM_STR);
        $stmt->bindParam(":FECHA_HASTA", $fechaHasta, \PDO::PARAM_STR);

        $stmt->execute();

        $estadisticas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $estadistica) {
                $estadisticas[] = new Estadistica($estadistica);
            }
        }

        return $estadisticas;
    }

    public function getTroncales($idEmpresa, $fechaDesde, $fechaHasta)
    {
        $db = SQLConnectPDO::GetConnection();

        $maxEnUnDia = "
        (
            SELECT 
                SUM(dr.DAR_VALUE)
            FROM vaa_daily_records dr
            WHERE dr.DAR_DATE = vaa_daily_records.DAR_DATE
            AND dr.dar_fdr_id < 1000
            AND dr.DAR_COMPANY_ID = :COMPANY_ID_2
        ) VALOR_TOTAL_EN_EL_DIA,
        ";

        // Query
        $sql = "
            SELECT 
                Convert(varchar, vaa_daily_records.DAR_DATE, 103) DAR_DATE,
                vaa_daily_records.DAR_DATE DAR_DATE_VALUE,
                vaa_fields_daily_records.FDR_ID,
                vaa_fields_daily_records.FDR_DESCRIPTION,
                vaa_daily_records.DAR_VALUE,
                vaa_fields_daily_records.FDR_UNIT_SINGULAR,
                vaa_fields_daily_records.FDR_UNIT_PLURAL,
                $maxEnUnDia
                (CASE WHEN vaa_daily_records.DAR_VALUE = 1 THEN vaa_fields_daily_records.FDR_UNIT_SINGULAR ELSE vaa_fields_daily_records.FDR_UNIT_PLURAL END) FDR_UNIT,
                (CASE WHEN 
                    (SELECT COUNT(1) 
                    FROM vaa_daily_records dos
                    WHERE dos.DAR_COMPANY_ID = vaa_daily_records.DAR_COMPANY_ID
                    AND dos.DAR_DATE = vaa_daily_records.DAR_DATE
                    AND dos.DAR_FDR_ID = 3000
                    ) > 0 THEN 1
                ELSE 0
                END) EXISTE_FDR_ID_3000
            FROM vaa_daily_records
            INNER JOIN vaa_fields_daily_records ON vaa_fields_daily_records.fdr_id = vaa_daily_records.dar_fdr_id
            WHERE 1 = 1
            AND DAR_DATE BETWEEN :FECHA_DESDE AND :FECHA_HASTA
            AND vaa_fields_daily_records.FDR_ID < 1000
        ";

        // Filtros
        $sql .= "AND DAR_COMPANY_ID = :COMPANY_ID ";
        $sql .= "ORDER BY vaa_daily_records.DAR_DATE ASC, vaa_daily_records.DAR_FDR_ID ASC  ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":COMPANY_ID_2", $idEmpresa, \PDO::PARAM_INT);

        $stmt->bindParam(":FECHA_DESDE", $fechaDesde, \PDO::PARAM_STR);
        $stmt->bindParam(":FECHA_HASTA", $fechaHasta, \PDO::PARAM_STR);

        $stmt->execute();

        $estadisticas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $estadistica) {
                $estadisticas[] = new Estadistica($estadistica);
            }
        }

        return $estadisticas;
    }

    public function getTroncalesMaximos($idEmpresa, $fechaDesde, $fechaHasta)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SELECT 
                Convert(varchar, vaa_daily_records.DAR_DATE, 103) DAR_DATE,
                vaa_daily_records.DAR_DATE DAR_DATE_VALUE,
                MAX(vaa_fields_daily_records.FDR_ID) MAX_TRONCALES_USADAS,
                (CASE WHEN 
                    (SELECT COUNT(1) 
                    FROM vaa_daily_records dos
                    WHERE dos.DAR_COMPANY_ID = vaa_daily_records.DAR_COMPANY_ID
                    AND dos.DAR_DATE = vaa_daily_records.DAR_DATE
                    AND dos.DAR_FDR_ID = 3000
                    ) > 0 THEN 1
                ELSE 0
                END) EXISTE_FDR_ID_3000
            FROM vaa_daily_records
            INNER JOIN vaa_fields_daily_records ON vaa_fields_daily_records.fdr_id = vaa_daily_records.dar_fdr_id
            WHERE 1 = 1
            AND DAR_DATE BETWEEN :FECHA_DESDE AND :FECHA_HASTA
            AND vaa_fields_daily_records.FDR_ID < 1000
        ";

        // Filtros
        $sql .= "AND DAR_COMPANY_ID = :COMPANY_ID ";
        $sql .= "   
            GROUP BY vaa_daily_records.DAR_DATE, vaa_daily_records.DAR_COMPANY_ID
            ORDER BY vaa_daily_records.DAR_DATE ASC 
        ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);

        $stmt->bindParam(":FECHA_DESDE", $fechaDesde, \PDO::PARAM_STR);
        $stmt->bindParam(":FECHA_HASTA", $fechaHasta, \PDO::PARAM_STR);

        $stmt->execute();

        $estadisticas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $estadistica) {
                $estadisticas[] = new Estadistica($estadistica);
            }
        }

        return $estadisticas;
    }

    public function getDetalleTroncal($idEmpresa, $fecha)
    {
        $db = SQLConnectPDO::GetConnection();

        $maxEnUnDia = "
        (
            SELECT 
                SUM(dr.DAR_VALUE)
            FROM vaa_daily_records dr
            WHERE dr.DAR_DATE = vaa_daily_records.DAR_DATE
            AND dr.dar_fdr_id < 1000
            AND dr.DAR_COMPANY_ID = :COMPANY_ID_2
        ) VALOR_TOTAL_EN_EL_DIA,
        ";
        
        // Query
        $sql = 
            "SELECT 
                Convert(varchar, vaa_daily_records.DAR_DATE, 103) DAR_DATE,
                vaa_daily_records.DAR_DATE DAR_DATE_VALUE,
                vaa_fields_daily_records.FDR_ID,
                vaa_fields_daily_records.FDR_DESCRIPTION,
                vaa_daily_records.DAR_VALUE,
                vaa_fields_daily_records.FDR_UNIT_SINGULAR,
                $maxEnUnDia
                vaa_fields_daily_records.FDR_UNIT_PLURAL
            FROM vaa_daily_records
            INNER JOIN vaa_fields_daily_records ON vaa_fields_daily_records.fdr_id = vaa_daily_records.dar_fdr_id
            WHERE 1 = 1
            AND DAR_DATE = :FECHA
            AND vaa_fields_daily_records.FDR_ID < 1000
        ";

        // Filtros
        $sql .= "AND DAR_COMPANY_ID = :COMPANY_ID ";
        $sql .= "ORDER BY vaa_daily_records.DAR_DATE ASC ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":COMPANY_ID_2", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindParam(":FECHA", $fecha, \PDO::PARAM_STR);

        $stmt->execute();

        $estadisticas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $estadistica) {
                $estadisticas[] = new Estadistica($estadistica);
            }
        }

        return $estadisticas;
    }

}
