<?php

namespace daoimpl;

use dao\IEmpresaDAO;
use \db\SQLConnectPDO;
use model\Empresa;
use \utils\LogUtils;

class EmpresaSQLDAO implements IEmpresaDAO  {

    public function getEmpresaPorURL($urlName)
    {
        $db = SQLConnectPDO::GetConnection();

        // Filtros
        $filtroEmpresa = "";
        $filtroEmpresa = " AND sec_parameters.PAR_COMPANY_ID = vaa2_companies.COM_ID";

        // Query
        $sql = "
            SELECT 
                COM_ID ID,
                COM_NAME NAME,
                COM_URL_NAME URL_NAME,
                COM_IMG IMG,
                COM_DNIS_REGEX_INT_GUIDE DNIS_REGEX,
                COM_DNIS_REGEX_EXT_GUIDE DNIS_REGEX_EXT,
                COM_OUTPUT_ROUTE OUTPUT_ROUTE,
                COM_ENABLED VAA_ACTIVE,
                COM_CANT_MAX_LINES CANT_MAX_LINES,
                COM_CANT_MAX_PHONE_BOOK CANT_MAX_PERSONAS,
                COM_CANT_MAX_DEPARTMENTS CANT_MAX_DEPARTAMENTOS,
                COM_CONTACT CONTACTO,
                COM_NOTES NOTAS,
                COM_TTS_MODE TTS_MODE
            FROM vaa2_companies
            WHERE 1 = 1
        ";
        $sql .= "AND COM_URL_NAME = :url_name ";
        $stmt = $db->prepare($sql);

        // Parametros
        $stmt->bindParam(":url_name", $urlName, \PDO::PARAM_STR);
        $stmt->execute();

        $empresas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $empresa) {
                $empresas[] = new Empresa($empresa);
            }
        }

        return $empresas;
    }

    public function getEmpresa($id=null)
    {
        $db = SQLConnectPDO::GetConnection();

        // Filtros
        $filtroEmpresa = "";
        $filtroEmpresa = " AND sec_parameters.PAR_COMPANY_ID = vaa2_companies.COM_ID";

        // Query
        $sql = "
            SELECT 
                COM_ID ID,
                COM_NAME NAME,
                COM_URL_NAME URL_NAME,
                COM_IMG IMG,
                COM_DNIS_REGEX_INT_GUIDE DNIS_REGEX,
                COM_DNIS_REGEX_EXT_GUIDE DNIS_REGEX_EXT,
                COM_OUTPUT_ROUTE OUTPUT_ROUTE,
                COM_ENABLED VAA_ACTIVE,
                COM_CANT_MAX_LINES CANT_MAX_LINES,
                COM_CANT_MAX_PHONE_BOOK CANT_MAX_PERSONAS,
                COM_CANT_MAX_DEPARTMENTS CANT_MAX_DEPARTAMENTOS,
                COM_CONTACT CONTACTO,
                COM_NOTES NOTAS,
                COM_TTS_MODE TTS_MODE
            FROM vaa2_companies
            WHERE 1 = 1
        ";
        if ($id != null) {
            $sql .= "AND COM_ID = :id ";
        }
        $sql .= " ORDER BY COM_NAME ASC ";
        $stmt = $db->prepare($sql);

        // Parametros
        if ($id != null) {
            $stmt->bindValue(":id", $id, \PDO::PARAM_INT);
        }
        $stmt->execute();

        $empresas = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $empresa) {
                $empresas[] = new Empresa($empresa);
            }
        }

        return $empresas;
    }

    public function guardar(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();
        if ($empresa->getId() == null) {
            $stmt = $db->prepare("
                Insert Into vaa2_companies 
                (COM_NAME, COM_URL_NAME, COM_DNIS_REGEX_INT_GUIDE, COM_DNIS_REGEX_EXT_GUIDE, COM_OUTPUT_ROUTE, COM_CONTACT, COM_NOTES, COM_ENABLED, COM_TTS_MODE) 
                Values 
                (:name, :url_name, :dnis_regex, :dnis_regex_ext, :output_route, '', '', 0, :tts_mode)
            ");

            $stmt->bindValue(":name", $empresa->getName(), \PDO::PARAM_STR);
            $stmt->bindValue(":url_name", $empresa->getUrlName(), \PDO::PARAM_STR);
            $stmt->bindValue(":dnis_regex", $empresa->getDnisRegex(), \PDO::PARAM_STR);
            $stmt->bindValue(":output_route", $empresa->getOutputRoute(), \PDO::PARAM_STR);
            $stmt->bindValue(":dnis_regex_ext", $empresa->getDnisRegexExt(), \PDO::PARAM_STR);
            $stmt->bindValue(":tts_mode", $empresa->getTts_mode(), \PDO::PARAM_STR);
        } else {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                UPDATE vaa2_companies SET
                COM_NAME = :name,
                COM_URL_NAME = :url_name
                Where vaa2_companies.COM_ID = :id 
            ");
            
            $stmt->bindValue(":name", $empresa->getName(), \PDO::PARAM_STR);
            $stmt->bindValue(":url_name", $empresa->getUrlName(), \PDO::PARAM_STR);
            $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        }

        $stmt->execute();

        return $db->lastInsertId();
    }

    public function eliminar($id)
    {
        $db = SQLConnectPDO::GetConnection();
        $sql = "
            DELETE VAA_TRANSFER_ACTION_PHB 
            WHERE tap_phb_id 
                IN (SELECT PHB_ID 
                    FROM VAA_PHONE_BOOK 
                    WHERE phb_dep_id 
                        IN (SELECT DEP_ID 
                            FROM VAA_DEPARTMENTS 
                            WHERE DEP_COMPANY_ID = :company_id))
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_PHONE_BOOK 
            WHERE phb_dep_id 
                IN (SELECT DEP_ID 
                    FROM VAA_DEPARTMENTS 
                    WHERE DEP_COMPANY_ID = :company_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE SEC_PARAMETERS
            WHERE par_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_HOLIDAYS
            WHERE hol_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_BANDS
            WHERE ban_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_TRANSFER_ACTION_DEP
            WHERE tad_dep_id
                IN (SELECT DEP_ID 
                    FROM VAA_DEPARTMENTS 
                    WHERE DEP_COMPANY_ID = :company_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_DEPARTMENTS
            WHERE dep_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_FAXES 
            WHERE fax_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_VOICE_MAILS
            WHERE vma_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA_DAILY_RECORDS
            WHERE dar_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA2_USER_ACCESSES_LOG
            WHERE ual_usr_id
                IN (SELECT usr_id 
                FROM VAA2_USERS 
                WHERE usr_company_id = :company_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA2_USER_ACCESSES_LOG
            WHERE ual_company_id_accessed = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA2_USER_CHANGES_LOG
            WHERE ucl_usr_id
                IN (SELECT usr_id 
                    FROM VAA2_USERS 
                    WHERE usr_company_id = :company_id)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA2_USERS
            WHERE usr_company_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE vaa2_dial_plan
            WHERE DPL_COMPANY_ID = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE VAA2_COMPANIES
            WHERE com_id = :company_id
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":company_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function setImage(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_IMG = :img
            WHERE vaa2_companies.COM_ID = :id "
        );
        
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":img", $empresa->getImg(), \PDO::PARAM_STR);
        $stmt->execute();

        return $db->lastInsertId();
    }

    public function setRegEx(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_DNIS_REGEX_INT_GUIDE = :dnis_regex
            WHERE vaa2_companies.COM_ID = :id 
        ");
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":dnis_regex", $empresa->getDnisRegex(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setRegExExt(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa2_companies Set
            COM_DNIS_REGEX_EXT_GUIDE = :dnis_regex_ext
            WHERE vaa2_companies.COM_ID = :id 
        ");
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":dnis_regex_ext", $empresa->getDnisRegexExt(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setOutputRoute(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_OUTPUT_ROUTE = :output_route
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":output_route", $empresa->getOutputRoute(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setCantMaxPersonas(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_CANT_MAX_PHONE_BOOK = :com_cant_max_phone_book
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":com_cant_max_phone_book", $empresa->getCant_max_personas(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setCantMaxDepartamentos(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_CANT_MAX_DEPARTMENTS = :com_cant_max_departments
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":com_cant_max_departments", $empresa->getCant_max_departamentos(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setCantMaxLineas(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_CANT_MAX_LINES = :com_cant_max_lines
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":com_cant_max_lines", $empresa->getCantMaxLines(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setContacto(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_CONTACT = :com_contact
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":com_contact", $empresa->getContacto(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setNotas(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_NOTES = :com_notes
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":com_notes", $empresa->getNotas(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setEnabled(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_ENABLED = 1
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setDisabled(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_ENABLED = 0
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setConfiguracionGeneral(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
                COM_DNIS_REGEX_INT_GUIDE = :dnis_regex,
                COM_OUTPUT_ROUTE = :output_route,
                COM_DNIS_REGEX_EXT_GUIDE = :dnis_regex_ext,
                COM_ENABLED = :vaa_active,
                COM_CANT_MAX_LINES = :cant_max_lines,
                COM_CANT_MAX_PHONE_BOOK = :cant_max_phone_book,
                COM_CANT_MAX_DEPARTMENTS = :cant_max_departments,
                COM_CONTACT = :contacto,
                COM_NOTES = :com_notes
            WHERE vaa2_companies.COM_ID = :id "
        );

        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":dnis_regex", $empresa->getDnisRegex(), \PDO::PARAM_STR);
        $stmt->bindValue(":output_route", $empresa->getOutputRoute(), \PDO::PARAM_STR);
        $stmt->bindValue(":dnis_regex_ext", $empresa->getDnisRegexExt(), \PDO::PARAM_STR);
        $stmt->bindValue(":vaa_active", $empresa->getVaaActive(), \PDO::PARAM_STR);
        $stmt->bindValue(":cant_max_lines", $empresa->getCantMaxLines(), \PDO::PARAM_STR);
        $stmt->bindValue(":cant_max_phone_book", $empresa->getCant_max_personas(), \PDO::PARAM_STR);
        $stmt->bindValue(":cant_max_departments", $empresa->getCant_max_departamentos(), \PDO::PARAM_STR);
        $stmt->bindValue(":contacto", $empresa->getContacto(), \PDO::PARAM_STR);
        $stmt->bindValue(":com_notes", $empresa->getNotas(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }

    public function setTTSMode(Empresa $empresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            UPDATE vaa2_companies SET
            COM_TTS_MODE = :tts_mode
            WHERE vaa2_companies.COM_ID = :id "
        );
        $stmt->bindValue(":id", $empresa->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(":tts_mode", $empresa->getTts_mode(), \PDO::PARAM_STR);
        $stmt->execute();

        return $empresa->getId();
    }
}
