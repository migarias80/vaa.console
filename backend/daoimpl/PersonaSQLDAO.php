<?php

namespace daoimpl;

use dao\IPersonaDAO;
use dao\PHB_GRAMMAR;
use \db\SQLConnectPDO;
use model\Persona;
use \dao\AGenericDAO;
use dao\FLAG_POS;
use model\PersonaMin;

class PersonaSQLDAO extends AGenericDAO implements IPersonaDAO
{

    public function getPersona($idEmpresa, $id=null, $id_dep=null, $is_sec=null, $min=false, $faxIdDay=null, $faxIdNight=null, $VMIdDay=null, $VMIdNight=null, $is_transf=null)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT 
                phb.*,
                dep.dep_name dep_name,
                ISNULL(phb.phb_last_name1, '') + ' ' + 
                ISNULL(phb.phb_last_name2, '') + ', ' + 
                ISNULL(phb.phb_first_name, '') + ' ' + 
                ISNULL(phb.phb_middle_name, '') phb_full_name,
                (SELECT
                    ISNULL(sec.phb_last_name1, '') + ' ' + 
                    ISNULL(sec.phb_last_name2, '') + ', ' + 
                    ISNULL(sec.phb_first_name, '') + ' ' + 
                    ISNULL(sec.phb_middle_name, '')
                FROM vaa_phone_book sec 
                WHERE sec.phb_id = phb.phb_sec_id) sec_name
            FROM vaa_phone_book phb
            INNER JOIN vaa_departments dep ON dep.dep_id = phb.phb_dep_id 
            WHERE 1 = 1
        ";

        $sql .= "AND dep.DEP_COMPANY_ID = :COMPANY_ID ";
        if ($id != null) {
            $sql .= "AND phb_id = :phb_id ";
        }
        if ($id_dep != null) {
            $sql .= "AND phb_dep_id = :phb_dep_id ";
        }
        if ($is_sec != null) {
            $sql .= "AND phb_is_sec = :phb_is_sec ";
        }
        if ($faxIdDay != null) {
            $sql .= "AND phb_fax_daytime = :phb_fax_daytime ";
        }
        if ($faxIdNight != null) {
            $sql .= "AND phb_fax_nighttime = :phb_fax_nighttime ";
        }
        if ($VMIdDay != null) {
            $sql .= "AND phb_vma_daytime = :phb_vma_daytime ";
        }
        if ($VMIdNight != null) {
            $sql .= "AND phb_vma_nighttime = :phb_vma_nighttime ";
        }
        if ($is_transf != null) {
            $sql .= "AND phb_is_transf = :phb_is_transf ";
        }
        $stmt = $db->prepare($sql);

        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        if ($id != null) {
            $stmt->bindParam(":phb_id", $id, \PDO::PARAM_INT);
        }
        if ($id_dep != null) {
            $stmt->bindParam(":phb_dep_id", $id_dep, \PDO::PARAM_INT);
        }
        if ($is_sec != null) {
            $val = $this->BooleanToString($is_sec);
            $stmt->bindParam(":phb_is_sec", $val, \PDO::PARAM_STR);
        }
        if ($faxIdDay != null) {
            $stmt->bindParam(":phb_fax_daytime", $faxIdDay, \PDO::PARAM_INT);
        }
        if ($faxIdNight != null) {
            $stmt->bindParam(":phb_fax_nighttime", $faxIdNight, \PDO::PARAM_INT);
        }
        if ($VMIdDay != null) {
            $stmt->bindParam(":phb_vma_daytime", $VMIdDay, \PDO::PARAM_INT);
        }
        if ($VMIdNight != null) {
            $stmt->bindParam(":phb_vma_nighttime", $VMIdNight, \PDO::PARAM_INT);
        }
        if ($is_transf != null) {
            $val = $this->BooleanToString($is_transf);
            $stmt->bindParam(":phb_is_transf", $val, \PDO::PARAM_STR);
        }
        $stmt->execute();

        $pers = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $per) {
                if ($min) {
                    $pers[] = new PersonaMin($per);
                } else {
                    $per['PHB_GE_ALLOW_PLAYBACK_INT_NUMBER'] = $this->StringToBoolean($per['PHB_GE_ALLOW_PLAYBACK_INT_NUMBER']);
                    $per['PHB_GI_ALLOW_PLAYBACK_INT_NUMBER'] = $this->StringToBoolean($per['PHB_GI_ALLOW_PLAYBACK_INT_NUMBER']);
                    $per['PHB_ALLOW_HTDF_EXT'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::ALLOW_HTDF_EXT);
                    $per['PHB_ALLOW_HTDF_INT'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::ALLOW_HTDF_INT);
                    $per['PHB_ALLOW_VOZ_EXT'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::ALLOW_VOZ_EXT);
                    $per['PHB_ALLOW_VOZ_INT'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::ALLOW_VOZ_INT);
                    $per['PHB_PLAY_MSG_INFO_TRANF'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::PLAY_MSG_INFO_TRANF);
                    $per['PHB_MSG_SEC_PERSONAL'] = $this->FlagDecode($per['PHB_FLAGS'], FLAG_POS::MSG_SEC_PERSONAL);

                    $phpGrammar = [];
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_PAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_PAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_PAPELLIDO_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_PAPELLIDO_SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_SNOMBRE_PAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_SNOMBRE_PAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_SNOMBRE_PAPELLIDO_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_SNOMBRE_PAPELLIDO_SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::SNOMBRE_PAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::SNOMBRE_PAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::SNOMBRE_PAPELLIDO_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::SNOMBRE_PAPELLIDO_SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::SNOMBRE_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::SNOMBRE_SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::SNOMBRE)) {
                        $phpGrammar[] = PHB_GRAMMAR::SNOMBRE;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PNOMBRE_SNOMBRE)) {
                        $phpGrammar[] = PHB_GRAMMAR::PNOMBRE_SNOMBRE;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::SAPELLIDO;
                    }
                    if ($this->FlagDecode($per['PHB_GRAMMAR'], PHB_GRAMMAR::PAPELLIDO_SAPELLIDO)) {
                        $phpGrammar[] = PHB_GRAMMAR::PAPELLIDO_SAPELLIDO;
                    }
                    $per['PHB_GRAMMAR_OPTIONS'] = $phpGrammar;
                    $pers[] = new Persona($per);
                }
            }
        }

        return $pers;
    }

    public function getPersonaMinNoCast($idEmpresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT 
                phb.phb_id,
                ISNULL(phb.phb_last_name1, '') + ' ' + 
                ISNULL(phb.phb_last_name2, '') + ', ' + 
                ISNULL(phb.phb_first_name, '') + ' ' + 
                ISNULL(phb.phb_middle_name, '') phb_full_name,
                dep.dep_name dep_name,
                phb.phb_daytime_number,
                phb.phb_nighttime_number,
                phb.phb_daytime_cellular,
                phb.phb_nighttime_cellular,
                (SELECT
                    ISNULL(sec.phb_last_name1, '') + ' ' + 
                    ISNULL(sec.phb_last_name2, '') + ', ' + 
                    ISNULL(sec.phb_first_name, '') + ' ' + 
                    ISNULL(sec.phb_middle_name, '')
                FROM vaa_phone_book sec 
                WHERE sec.phb_id = phb.phb_sec_id) sec_name
            FROM vaa_phone_book phb
            INNER JOIN vaa_departments dep ON dep.dep_id = phb.phb_dep_id 
            WHERE 1 = 1
        ";

        $sql .= "AND dep.DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();

        $pers = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $per) {
                $pers[] = $per;
            }
        }

        return $pers;
    }

    public function guardar(Persona $persona)
    {
        $persona->setPhbGeAllowPlaybackIntNumber($this->BooleanToString($persona->getPhbGeAllowPlaybackIntNumber()));
        $persona->setPhbGiAllowPlaybackIntNumber($this->BooleanToString($persona->getPhbGiAllowPlaybackIntNumber()));
        $persona->setPhbIsSec($this->BooleanToString($persona->getPhbIsSec()));
        $persona->setPhbIsTransf($this->BooleanToString($persona->getPhbIsTransf()));
        $flagValue = $this->FlagEnconde(0, $persona->getPhbAllowHtdfExt(), FLAG_POS::ALLOW_HTDF_EXT);
        $flagValue = $this->FlagEnconde($flagValue, $persona->getPhbAllowHtdfInt(), FLAG_POS::ALLOW_HTDF_INT);
        $flagValue = $this->FlagEnconde($flagValue, $persona->getPhbAllowVozExt(), FLAG_POS::ALLOW_VOZ_EXT);
        $flagValue = $this->FlagEnconde($flagValue, $persona->getPhbAllowVozInt(), FLAG_POS::ALLOW_VOZ_INT);
        $flagValue = $this->FlagEnconde($flagValue, $persona->getPhbPlayMsgInfoTranf(), FLAG_POS::PLAY_MSG_INFO_TRANF);
        $flagValue = $this->FlagEnconde($flagValue, $persona->getPhbMsgSecPersonal(), FLAG_POS::MSG_SEC_PERSONAL);
        $persona->setPhbFlags($flagValue);

        $grammarOptionValue = 0;
        foreach ($persona->getPhbGrammarOptions() as $grammarOption) {
            $grammarOptionValue = $this->FlagEnconde($grammarOptionValue, 1, $grammarOption);
        }
        $persona->setPhbGrammar($grammarOptionValue);

        $db = SQLConnectPDO::GetConnection();
        if ($persona->getPhbId() == null) {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                INSERT INTO vaa_phone_book
                (phb_last_update_utc, phb_dep_id, phb_first_name, phb_middle_name, phb_last_name1, phb_last_name2, phb_nick_name, phb_email, phb_is_sec, phb_sec_id, phb_daytime_number, phb_nighttime_number, phb_daytime_cellular, phb_nighttime_cellular, phb_flags, phb_int_guide_number, phb_ext_guide_number, phb_fax_daytime, phb_fax_nighttime, phb_dialpost_number_fax_daytime, phb_dialpost_number_fax_nighttime, phb_vma_daytime, phb_vma_nighttime, phb_dialpost_number_vma_daytime, phb_dialpost_number_vma_nighttime, phb_grammar, phb_confirmation, phb_fon_first_name, phb_fon_middle_name, phb_fon_last_name1, phb_fon_last_name2, phb_ext_access_key, phb_gi_allow_playback_int_number, phb_ge_allow_playback_int_number, phb_is_transf)
                VALUES
                (getdate(), :phb_dep_id, :phb_first_name, :phb_middle_name, :phb_last_name1, :phb_last_name2, :phb_nick_name, :phb_email, :phb_is_sec, :phb_sec_id, :phb_daytime_number, :phb_nighttime_number, :phb_daytime_cellular, :phb_nighttime_cellular, :phb_flags, :phb_int_guide_number, :phb_ext_guide_number, :phb_fax_daytime, :phb_fax_nighttime, :phb_dialpost_number_fax_daytime, :phb_dialpost_number_fax_nighttime, :phb_vma_daytime, :phb_vma_nighttime, :phb_dialpost_number_vma_daytime, :phb_dialpost_number_vma_nighttime, :phb_grammar, :phb_confirmation, :phb_fon_first_name, :phb_fon_middle_name, :phb_fon_last_name1, :phb_fon_last_name2, :phb_ext_access_key, :phb_gi_allow_playback_int_number, :phb_ge_allow_playback_int_number, :phb_is_transf)
            ");
        } else {
            $stmt = $db->prepare("
                SET NOCOUNT ON;
                UPDATE vaa_phone_book SET
                    phb_last_update_utc = getdate(), 
                    phb_dep_id = :phb_dep_id,
                    phb_first_name = :phb_first_name, 
                    phb_middle_name = :phb_middle_name, 
                    phb_last_name1 = :phb_last_name1, 
                    phb_last_name2 = :phb_last_name2, 
                    phb_nick_name = :phb_nick_name, 
                    phb_email = :phb_email, 
                    phb_is_sec = :phb_is_sec, 
                    phb_sec_id = :phb_sec_id, 
                    phb_daytime_number = :phb_daytime_number, 
                    phb_nighttime_number = :phb_nighttime_number, 
                    phb_daytime_cellular = :phb_daytime_cellular, 
                    phb_nighttime_cellular = :phb_nighttime_cellular, 
                    phb_flags = :phb_flags, 
                    phb_int_guide_number = :phb_int_guide_number, 
                    phb_ext_guide_number = :phb_ext_guide_number, 
                    phb_fax_daytime = :phb_fax_daytime, 
                    phb_fax_nighttime = :phb_fax_nighttime, 
                    phb_dialpost_number_fax_daytime = :phb_dialpost_number_fax_daytime, 
                    phb_dialpost_number_fax_nighttime = :phb_dialpost_number_fax_nighttime, 
                    phb_vma_daytime = :phb_vma_daytime, 
                    phb_vma_nighttime = :phb_vma_nighttime, 
                    phb_dialpost_number_vma_daytime = :phb_dialpost_number_vma_daytime, 
                    phb_dialpost_number_vma_nighttime = :phb_dialpost_number_vma_nighttime, 
                    phb_grammar = :phb_grammar, 
                    phb_confirmation = :phb_confirmation, 
                    phb_fon_first_name = :phb_fon_first_name, 
                    phb_fon_middle_name = :phb_fon_middle_name, 
                    phb_fon_last_name1 = :phb_fon_last_name1,
                    phb_fon_last_name2 = :phb_fon_last_name2, 
                    phb_ext_access_key = :phb_ext_access_key, 
                    phb_gi_allow_playback_int_number = :phb_gi_allow_playback_int_number, 
                    phb_ge_allow_playback_int_number = :phb_ge_allow_playback_int_number,
                    phb_is_transf = :phb_is_transf
                WHERE vaa_phone_book.phb_id = :phb_id 
            ");
            $stmt->bindValue(":phb_id", $persona->getPhbId(), \PDO::PARAM_INT);
        }
        $stmt->bindValue(":phb_dep_id", $persona->getPhbDepId(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_first_name", $persona->getPhbFirstName(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_middle_name", $persona->getPhbMiddleName(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_last_name1", $persona->getPhbLastName1(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_last_name2", $persona->getPhbLastName2(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_nick_name", $persona->getPhbNickName(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_email", $persona->getPhbEmail(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_is_sec", $persona->getPhbIsSec(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_sec_id", $persona->getPhbSecId(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_daytime_number", $persona->getPhbDaytimeNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_nighttime_number", $persona->getPhbNighttimeNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_daytime_cellular", $persona->getPhbDaytimeCellular(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_nighttime_cellular", $persona->getPhbNighttimeCellular(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_flags", $persona->getPhbFlags(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_int_guide_number", $persona->getPhbIntGuideNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_ext_guide_number", $persona->getPhbExtGuideNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_fax_daytime", $persona->getPhbFaxDaytime(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_fax_nighttime", $persona->getPhbFaxNighttime(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_dialpost_number_fax_daytime", $persona->getPhbDialpostNumberFaxDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_dialpost_number_fax_nighttime", $persona->getPhbDialpostNumberFaxNighttime(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_vma_daytime", $persona->getPhbVmaDaytime(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_vma_nighttime", $persona->getPhbVmaNighttime(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_dialpost_number_vma_daytime", $persona->getPhbDialpostNumberVmaDaytime(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_dialpost_number_vma_nighttime", $persona->getPhbDialpostNumberVmaNighttime(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_grammar", $persona->getPhbGrammar(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_confirmation", $persona->getPhbConfirmation(), \PDO::PARAM_INT);
        $stmt->bindValue(":phb_fon_first_name", $persona->getPhbFonFirstName(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_fon_middle_name", $persona->getPhbFonMiddleName(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_fon_last_name1", $persona->getPhbFonLastName1(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_fon_last_name2", $persona->getPhbFonLastName2(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_ext_access_key", $persona->getPhbExtAccessKey(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_gi_allow_playback_int_number", $persona->getPhbGiAllowPlaybackIntNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_ge_allow_playback_int_number", $persona->getPhbGeAllowPlaybackIntNumber(), \PDO::PARAM_STR);
        $stmt->bindValue(":phb_is_transf", $persona->getPhbIsTransf(), \PDO::PARAM_STR);

        $stmt->execute();

        if ($persona->getPhbId() == null) {
            return $db->lastInsertId();
        } else {
            return $persona->getPhbId();
        }
    }

    public function eliminar($idEmpresa, $id)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare(
            "SET NOCOUNT ON;
            DELETE FROM vaa_phone_book 
            WHERE phb_id = :phb_id 
            AND vaa_phone_book.phb_dep_id = 
                (SELECT 
                    vaa_departments.dep_id 
                FROM vaa_departments 
                WHERE 
                    vaa_departments.dep_id = vaa_phone_book.phb_dep_id 
                    AND vaa_departments.DEP_COMPANY_ID = :COMPANY_ID
                )"
        );

        $stmt->bindValue(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->bindValue(":phb_id", $id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function desvincularSecretaria($id_secretaria)
    {
        $db = SQLConnectPDO::GetConnection();

        $stmt = $db->prepare("
            SET NOCOUNT ON;
            UPDATE vaa_phone_book SET 
                phb_sec_id = null
            WHERE phb_sec_id = :sec_id 
        ");

        $stmt->bindValue(":sec_id", $id_secretaria, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCountPersonas($idEmpresa)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SELECT 
                COUNT(phb.phb_id) AS CANTIDAD
            FROM vaa_phone_book phb
            INNER JOIN vaa_departments dep ON dep.dep_id = phb.phb_dep_id 
            WHERE 1 = 1
        ";

        $sql .= "AND dep.DEP_COMPANY_ID = :COMPANY_ID ";
        $stmt = $db->prepare($sql);

        $stmt->bindParam(":COMPANY_ID", $idEmpresa, \PDO::PARAM_INT);
        $stmt->execute();

        $pers = array();
        if ($stmt->rowCount()) {
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $per) {
                return $per['CANTIDAD'];
            }
        }

        return 0;
    }

    public function clearFaxDay($idEmpresa, $idFax)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_phone_book 
            SET phb_fax_daytime = null
            WHERE phb_fax_daytime = :fax_id
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(":fax_id", $idFax, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearFaxNight($idEmpresa, $idFax)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_phone_book 
            SET phb_fax_nighttime = null
            WHERE phb_fax_nighttime = :fax_id
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(":fax_id", $idFax, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearVoiceMailDay($idEmpresa, $idVoiceMail)
    {
        $db = SQLConnectPDO::GetConnection();

        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_phone_book 
            SET phb_vma_daytime = null
            WHERE phb_vma_daytime = :vma_id
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(":vma_id", $idVoiceMail, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function clearVoiceMailNight($idEmpresa, $idVoiceMail)
    {
        $db = SQLConnectPDO::GetConnection();
    
        $sql = "
            SET NOCOUNT ON;
            UPDATE vaa_phone_book 
            SET phb_vma_nighttime = null
            WHERE phb_vma_nighttime = :vma_id
        ";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(":vma_id", $idVoiceMail, \PDO::PARAM_INT);
        $stmt->execute();
    }

}