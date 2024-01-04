<?php

namespace daoimpl;

use \dao\IParametroDAO;
use \db\SQLConnectPDO;
use model\BandaHoraria;
use \model\ConfirmationOption;
use model\DayType;
use model\Feriado;
use model\OperationMode;
use model\Parametro;
use model\QueryValidation;
use model\TransferOption;
use model\GrammarOption;

class ParametroSQLDAO implements IParametroDAO
{

    public function getConfirmationOptions()
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT * 
            FROM vaa_confirmation_options 
            WHERE 1 = 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $confirmationOptions = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $option) {
                $confirmationOptions[] = new ConfirmationOption($option);
            }
        }

        return $confirmationOptions;
    }

    public function getTransferOption($taoCategoriesExclude=null)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT 
                vaa_transfer_action_options.* 
            FROM vaa_transfer_action_options 
            WHERE 1 = 1
        ";

        if ($taoCategoriesExclude != null) {
            $sql .= "AND tao_category not in (";
            $taoCategoriesExclude = implode(",", $taoCategoriesExclude);
            $sql .= $taoCategoriesExclude . ") ";
        }

        $sql .= "ORDER BY tao_category asc, tao_id asc";
        // $sql .= "ORDER BY tao_description asc";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $transferOptions = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $option) {
                $transferOptions[] = new TransferOption($option);
            }
        }

        return $transferOptions;
    }

    public function getOpcionesGramaticas($taoCategoriesExclude=null)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT * 
            FROM vaa_insert_grammar_options 
            WHERE 1 = 1
        ";

        $sql .= "ORDER BY oig_id asc";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $grammarOptionOptions = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $option) {
                $grammarOptionOptions[] = new GrammarOption($option);
            }
        }

        return $grammarOptionOptions;
    }

    public function getDayTypes()
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT * 
            FROM vaa_day_types 
            WHERE 1 = 1
            ORDER BY 
            CASE 
                WHEN DAT_DAY_TYPE = 'LUNES' THEN 2
                WHEN DAT_DAY_TYPE = 'MARTES' THEN 3
                WHEN DAT_DAY_TYPE = 'MIERCOLES' THEN 4
                WHEN DAT_DAY_TYPE = 'JUEVES' THEN 5
                WHEN DAT_DAY_TYPE = 'VIERNES' THEN 6
                WHEN DAT_DAY_TYPE = 'SABADO' THEN 7
                WHEN DAT_DAY_TYPE = 'DOMINGO' THEN 8
                WHEN DAT_DAY_TYPE = 'FERIADO' THEN 9
                WHEN DAT_DAY_TYPE = 'DEFECTO' THEN 10
                ELSE 11
            END ASC 
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $dayTypes = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $dayType) {
                $dayTypes[] = new DayType($dayType);
            }
        }

        return $dayTypes;
    }

    public function getOperationModes()
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT * 
            FROM vaa_operation_mode 
            WHERE 1 = 1
        ";

        $stmt = $db->prepare($sql);

        $stmt->execute();

        $operationModes = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $operationMode) {
                $operationModes[] = new OperationMode($operationMode);
            }
        }

        return $operationModes;
    }

    public function getBandasHorarias($idEmpresa)
    {
        $db = SQLConnectPDO::GetConnection();

        // Filtros
        $filtroEmpresa = "";
        $filtroEmpresa = "AND BAN_COMPANY_ID = :COMPANY_ID ";

        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",vaa_bands.BAN_COMPANY_ID COMPANY_ID ";

        // Query
        $sql = "
            SELECT 
                vaa_bands.*
                $selectEmpresa
            FROM vaa_bands 
            WHERE 1 = 1
            $filtroEmpresa
            ORDER BY 
            CASE 
                WHEN BAN_DAY_TYPE = 'DOMINGO' THEN 1
                WHEN BAN_DAY_TYPE = 'LUNES' THEN 2
                WHEN BAN_DAY_TYPE = 'MARTES' THEN 3
                WHEN BAN_DAY_TYPE = 'MIERCOLES' THEN 4
                WHEN BAN_DAY_TYPE = 'JUEVES' THEN 5
                WHEN BAN_DAY_TYPE = 'VIERNES' THEN 6
                WHEN BAN_DAY_TYPE = 'SABADO' THEN 7
                WHEN BAN_DAY_TYPE = 'FERIADO' THEN 8
                WHEN BAN_DAY_TYPE = 'DEFECTO' THEN 9
                ELSE 10
            END ASC, 
            BAN_START_HOUR ASC
        ";

        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();

        $bandas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $banda) {
                $bandas[] = new BandaHoraria($banda);
            }
        }

        return $bandas;
    }

    public function crearBandaHoraria(BandaHoraria $bandaHoraria)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            INSERT INTO vaa_bands
            (ban_day_type, ban_start_hour, ban_opm_code, ban_end_hour, ban_description, BAN_COMPANY_ID)
            VALUES
            (:ban_day_type, :ban_start_hour, :ban_opm_code, :ban_end_hour, :ban_description, :COMPANY_ID) 
        ");

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $bandaHoraria->getBusinessId(), \PDO::PARAM_INT);
        $stmt->bindValue(":ban_day_type", $bandaHoraria->getBanDayType(), \PDO::PARAM_STR);
        $stmt->bindValue(":ban_start_hour", $bandaHoraria->getBanStartHour(), \PDO::PARAM_STR);
        $stmt->bindValue(":ban_opm_code", $bandaHoraria->getBanOpmCode(), \PDO::PARAM_STR);
        $stmt->bindValue(":ban_end_hour", $bandaHoraria->getBanEndHour(), \PDO::PARAM_STR);
        $stmt->bindValue(":ban_description", $bandaHoraria->getBanDescription(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function generarBandasHorariasParaUnaEmpresa($idEmpresa)
    {
        $db = SQLConnectPDO::GetConnection();
        $stmt = $db->prepare("
            SET NOCOUNT ON;
            INSERT INTO VAA_BANDS 
            (BAN_DAY_TYPE, BAN_START_HOUR, BAN_OPM_CODE, BAN_END_HOUR, BAN_DESCRIPTION, BAN_COMPANY_ID)
            Select BAN_DAY_TYPE, BAN_START_HOUR, BAN_OPM_CODE, BAN_END_HOUR, BAN_DESCRIPTION, :COMPANY_ID
            FROM VAA_BANDS
            WHERE BAN_COMPANY_ID = 1
        ");

        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function eliminarBandaHoraria($idEmpresa, $dayType, $opmCode, $start, $end)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_bands 
            WHERE ban_day_type = :ban_day_type 
            AND ban_start_hour = :ban_start_hour
            AND ban_end_hour = :ban_end_hour
        ";

        // Filtros
        $sql .= "AND BAN_COMPANY_ID = :COMPANY_ID ";

        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":ban_day_type", $dayType, \PDO::PARAM_STR);
        $stmt->bindValue(":ban_start_hour", $start, \PDO::PARAM_STR);
        $stmt->bindValue(":ban_end_hour", $end, \PDO::PARAM_STR);

        $stmt->execute();
    }

    public function eliminarBandaHorariaPorDia($idEmpresa, $dayType)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_bands 
            WHERE ban_day_type = :ban_day_type ";

        // Filtros
        $sql .= "And BAN_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":ban_day_type", $dayType, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getParametros($idEmpresa, $parametrosFilter=null) {
        $db = SQLConnectPDO::GetConnection();

        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",PAR_COMPANY_ID COMPANY_ID ";

        // Query
        $sql = "
            SELECT 
                sec_parameters.*
                $selectEmpresa
            FROM sec_parameters 
            WHERE 1 = 1
        ";

        // Filtros
        $sql .= "And PAR_COMPANY_ID = :COMPANY_ID ";
        if ($parametrosFilter != null) {
            $sql .= "AND sec_parameters.par_name in (";
            $parametrosFilter = "'" . implode("','", $parametrosFilter) . "'";
            $sql .= $parametrosFilter . ") ";
        }
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);

        $stmt->execute();

        $parameters = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $parameter) {
                $parameters[] = new Parametro($parameter);
            }
        }
        return $parameters;
    }

    // -- Se quito esta funcionalidad
    public function getQueryValidation($parName, $idEmpresa)
    {
        // 1) Get dynamic query from query_id
        $filtroEmpresa = " AND PAR_COMPANY_ID = :COMPANY_ID";
        $querys = $this->getSQLQueryValidation($filtroEmpresa);

        $db = SQLConnectPDO::GetConnection();

        $filtroEmpresa2 = " AND PAR_COMPANY_ID = :COMPANY_ID";
        
        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",PAR_COMPANY_ID COMPANY_ID ";

        $sql = "
            SELECT 
                sec_parameters.*
                $selectEmpresa
            FROM sec_parameters 
            WHERE 1 = 1
            AND par_name = :par_name
            $filtroEmpresa2
        ";

        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":par_name", $parName, \PDO::PARAM_STR);
        $stmt->execute();

        $queryId = null;
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $parameter) {
                $queryId = $parameter['QUERY_ID'];
            }
        }

        if ($queryId == null) {
            return null;
        }

        // 2) Excecute dynamic query
        $db = SQLConnectPDO::GetConnection();

        $sql = $querys[$queryId-1];

        $stmt = $db->prepare($sql);

        // Parametros
        if (strpos($sql, ':COMPANY_ID') !== false) {
            $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);;
        }
        $stmt->execute();

        $queryValidations = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $queryValidation) {
                $queryValidation['value'] =  $queryValidation['value_1'];
                $queryValidation['desc'] =  $queryValidation['desc_1'];
                $queryValidations[] = new QueryValidation($queryValidation);
            }
        }
        return $queryValidations;
    }

    // -- Se quito esta funcionalidad
    public function getQueryValidationByQueryId($queryId, $idEmpresa)
    {
        // 1) Get dynamic query from query_id
        $filtroEmpresa = "";
        $filtroEmpresa = " AND PAR_COMPANY_ID = :COMPANY_ID";
        $querys = $this->getSQLQueryValidation($filtroEmpresa);

        // 2) Excecute dynamic query
        $db = SQLConnectPDO::GetConnection();

        $sql = $querys[$queryId-1];

        $stmt = $db->prepare($sql);

        // Parametros
        if (strpos($sql, ':COMPANY_ID') !== false) {
            $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);;
        }
        $stmt->execute();

        $queryValidations = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $queryValidation) {
                $queryValidation['value'] =  $queryValidation['value_1'];
                $queryValidation['desc'] =  $queryValidation['desc_1'];
                $queryValidations[] = new QueryValidation($queryValidation);
            }
        }
        return $queryValidations;
    }

    public function modificarParametro(Parametro $parametro)
    {
        $db = SQLConnectPDO::GetConnection();

        if ($parametro->getParValue() == null) {
            $parametro->setParValue("");
        }

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE sec_parameters SET
                par_last_update_utc = getdate(),
                par_value = :par_value
            WHERE sec_parameters.par_name = :par_name
        ";

        // Filtros
        $sql .= "AND sec_parameters.PAR_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $parametro->getBusinessId(), \PDO::PARAM_INT);
        $stmt->bindValue(":par_value", $parametro->getParValue(), \PDO::PARAM_STR);
        $stmt->bindValue(":par_name", $parametro->getParName(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function generarParametrosParaUnaEmpresa($idEmpresa)
    {
        if (!is_numeric($idEmpresa)) {
            throw new \Exception("Parametro incorrecto");
            return;
        }

        // Insert de parametros en base al select
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            SET NOCOUNT ON;
            INSERT INTO sec_parameters
            SELECT 
                PAR_NAME,
                PAR_DESCRIPTION,
                PAR_TYPE,
                PAR_VALUE,
                getdate() PAR_LAST_UPDATE_UTC,
                $idEmpresa PAR_COMPANY_ID
            FROM sec_parameters
            WHERE PAR_COMPANY_ID = 1
            AND PAR_TYPE != 'EDS_CONFIG' 
        ";

        // Filtros
        $sql .= "AND sec_parameters.PAR_NAME != 'VOX_DIRECTORY' ";
        $sql .= "AND sec_parameters.PAR_NAME != 'MHC_FILE' ";
        $stmt = $db->prepare($sql);

        $stmt->execute();
    }

    public function getFeriados($idEmpresa, $holDate = null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Filtros
        $filtroEmpresa = "";
        $filtroEmpresa = "AND HOL_COMPANY_ID = :COMPANY_ID ";

        // Select col empresa
        $selectEmpresa = "";
        $selectEmpresa .= ",vaa_holidays.HOL_COMPANY_ID COMPANY_ID ";

        // Query
        $sql = "
            SELECT 
                vaa_holidays.*
                $selectEmpresa
            FROM vaa_holidays 
            WHERE 1 = 1
            $filtroEmpresa
            ORDER BY HOL_DATE DESC
        ";

        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($holDate != null) {
            $stmt->bindParam(":hol_date", $hol_date, \PDO::PARAM_STR);
        }
        $stmt->execute();

        $feriados = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $feriado) {
                $feriados[] = new Feriado($feriado);
            }
        }

        return $feriados;
    }

    public function crearFeriado(Feriado $feriado)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            INSERT INTO vaa_holidays
            (hol_date, hol_day_type, hol_description, HOL_COMPANY_ID) 
            VALUES 
            (:hol_date, :hol_day_type, :hol_description, :COMPANY_ID) "
        );

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $feriado->getBusinessId(), \PDO::PARAM_INT);
        $stmt->bindValue(":hol_date", $feriado->getHolDate(), \PDO::PARAM_STR);
        $stmt->bindValue(":hol_description", $feriado->getHolDescription(), \PDO::PARAM_STR);
        $stmt->bindValue(":hol_day_type", $feriado->getHolDateType(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function eliminarFeriado($idEmpresa, $holDate)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            DELETE FROM vaa_holidays 
            WHERE hol_date = :hol_date 
        ";

        // Filtros
        $sql .= "AND HOL_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":hol_date", $holDate, \PDO::PARAM_STR);
        $stmt->execute();
    }

    // TODO: Obsoleto
    private function getSQLQueryValidation($filtroEmpresa) {
        $querys = array(
            "SELECT FAX_ID as value_1, FAX_DESCRIPTION as desc_1 FROM VAA_FAXES WHERE FAX_ENABLED_DAYTIME = 'T' $filtroEmpresa",
            "SELECT FAX_ID as value_1, FAX_DESCRIPTION as desc_1 FROM VAA_FAXES WHERE FAX_ENABLED_NIGHTTIME = 'T' $filtroEmpresa",
            "SELECT TAO_ID as value_1, TAO_DESCRIPTION as desc_1 FROM VAA_TRANSFER_ACTION_OPTIONS WHERE TAO_CATEGORY <> 5",
            "SELECT TAO_ID as value_1, TAO_DESCRIPTION as desc_1 FROM VAA_TRANSFER_ACTION_OPTIONS WHERE TAO_CATEGORY = 5",
            "SELECT VMA_ID as value_1, VMA_DESCRIPTION as desc_1 FROM VAA_VOICE_MAILS WHERE VMA_ENABLED_DAYTIME = 'T' $filtroEmpresa",
            "SELECT VMA_ID as value_1, VMA_DESCRIPTION as desc_1 FROM VAA_VOICE_MAILS WHERE VMA_ENABLED_NIGHTTIME = 'T' $filtroEmpresa"
        );

        return $querys;
    }

    public function clearFaxDay($idEmpresa, $idFax)
    {
        $db = SQLConnectPDO::GetConnection();

        // Query
        $sql = "
            SET NOCOUNT ON;
            UPDATE sec_parameters 
            SET par_value = ''
            WHERE par_name = 'FAX_DAY'
            AND par_value = :fax_id
        ";

        // Filtros
        $sql .= "AND PAR_COMPANY_ID = :COMPANY_ID ";
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
            UPDATE sec_parameters 
            SET par_value = ''
            WHERE par_name = 'FAX_NIGHT'
            AND par_value = :fax_id
        ";

        // Filtros
        $sql .= "AND PAR_COMPANY_ID = :COMPANY_ID ";
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
            UPDATE sec_parameters 
            SET par_value = ''
            WHERE par_name = 'VOICE_MAIL_DAY'
            AND par_value = :vma_id
        ";

        // Filtros
        $sql .= "AND PAR_COMPANY_ID = :COMPANY_ID ";
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
            UPDATE sec_parameters 
            SET par_value = ''
            WHERE par_name = 'VOICE_MAIL_NIGHT'
            AND par_value = :vma_id
        ";

        // Filtros
        $sql .= "AND PAR_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":vma_id", $idVoiceMail, \PDO::PARAM_INT);
        $stmt->execute();
    }

}