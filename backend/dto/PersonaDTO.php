<?php

namespace dto;

use \model\Department;
use model\Persona;
use \utils\CommonUtils;

class PersonaDTO implements IGenericDTO
{
    private $phb_id;
    private $phb_dep_id;
    private $phb_first_name;
    private $phb_middle_name;
    private $phb_last_name1;
    private $phb_last_name2;
    private $phb_nick_name;
    private $phb_email;
    private $phb_is_sec;
    private $phb_sec_id;
    private $phb_daytime_number;
    private $phb_nighttime_number;
    private $phb_daytime_cellular;
    private $phb_nighttime_cellular;
    private $phb_flags;
    private $phb_int_guide_number;
    private $phb_ext_guide_number;
    private $phb_fax_daytime;
    private $phb_fax_nighttime;
    private $phb_dialpost_number_fax_daytime;
    private $phb_dialpost_number_fax_nighttime;
    private $phb_vma_daytime;
    private $phb_vma_nighttime;
    private $phb_dialpost_number_vma_daytime;
    private $phb_dialpost_number_vma_nighttime;
    private $phb_grammar;
    private $phb_confirmation;
    private $phb_fon_first_name;
    private $phb_fon_middle_name;
    private $phb_fon_last_name1;
    private $phb_fon_last_name2;
    private $phb_last_update_utc;
    private $phb_ext_access_key;
    private $phb_gi_allow_playback_int_number;
    private $phb_ge_allow_playback_int_number;
    private $phb_allow_htdf_ext; // Flag
    private $phb_allow_htdf_int; // Flag
    private $phb_allow_voz_ext; // Flag
    private $phb_allow_voz_int; // Flag
    private $phb_play_msg_info_tranf; // Flag
    private $phb_msg_sec_personal; // Flag
    private $phb_is_transf;

    private $transfer_options;
    private $business_id;
    private $dep_name;
    private $phb_full_name;
    private $phb_grammar_options; // Array phb_grammar
    private $sec_name;
	private $COMPANY_ID;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setPhbId(CommonUtils::GetArrayValue('PHB_ID', $data));
            $this->setPhbDepId(CommonUtils::GetArrayValue('PHB_DEP_ID', $data));
            $this->setPhbFirstName(CommonUtils::GetArrayValue('PHB_FIRST_NAME', $data));
            $this->setPhbMiddleName(CommonUtils::GetArrayValue('PHB_MIDDLE_NAME', $data));
            $this->setPhbLastName1(CommonUtils::GetArrayValue('PHB_LAST_NAME1', $data));
            $this->setPhbLastName2(CommonUtils::GetArrayValue('PHB_LAST_NAME2', $data));
            $this->setPhbNickName(CommonUtils::GetArrayValue('PHB_NICK_NAME', $data));
            $this->setPhbEmail(CommonUtils::GetArrayValue('PHB_EMAIL', $data));
            $this->setPhbIsSec(CommonUtils::GetArrayValue('PHB_IS_SEC', $data));
            $this->setPhbSecId(CommonUtils::GetArrayValue('PHB_SEC_ID', $data));
            $this->setPhbDaytimeNumber(CommonUtils::GetArrayValue('PHB_DAYTIME_NUMBER', $data));
            $this->setPhbNighttimeNumber(CommonUtils::GetArrayValue('PHB_NIGHTTIME_NUMBER', $data));
            $this->setPhbDaytimeCellular(CommonUtils::GetArrayValue('PHB_DAYTIME_CELLULAR', $data));
            $this->setPhbNighttimeCellular(CommonUtils::GetArrayValue('PHB_NIGHTTIME_CELLULAR', $data));
            $this->setPhbFlags(CommonUtils::GetArrayValue('PHB_FLAGS', $data));
            $this->setPhbIntGuideNumber(CommonUtils::GetArrayValue('PHB_INT_GUIDE_NUMBER', $data));
            $this->setPhbExtGuideNumber(CommonUtils::GetArrayValue('PHB_EXT_GUIDE_NUMBER', $data));
            $this->setPhbFaxDaytime(CommonUtils::GetArrayValue('PHB_FAX_DAYTIME', $data));
            $this->setPhbFaxNighttime(CommonUtils::GetArrayValue('PHB_FAX_NIGHTTIME', $data));
            $this->setPhbDialpostNumberFaxDaytime(CommonUtils::GetArrayValue('PHB_DIALPOST_NUMBER_FAX_DAYTIME', $data));
            $this->setPhbDialpostNumberFaxNighttime(CommonUtils::GetArrayValue('PHB_DIALPOST_NUMBER_FAX_NIGHTTIME', $data));
            $this->setPhbVmaDaytime(CommonUtils::GetArrayValue('PHB_VMA_DAYTIME', $data));
            $this->setPhbVmaNighttime(CommonUtils::GetArrayValue('PHB_VMA_NIGHTTIME', $data));
            $this->setPhbDialpostNumberVmaDaytime(CommonUtils::GetArrayValue('PHB_DIALPOST_NUMBER_VMA_DAYTIME', $data));
            $this->setPhbDialpostNumberVmaNighttime(CommonUtils::GetArrayValue('PHB_DIALPOST_NUMBER_VMA_NIGHTTIME', $data));
            $this->setPhbGrammar(CommonUtils::GetArrayValue('PHB_GRAMMAR', $data));
            $this->setPhbConfirmation(CommonUtils::GetArrayValue('PHB_CONFIRMATION', $data));
            $this->setPhbFonFirstName(CommonUtils::GetArrayValue('PHB_FON_FIRST_NAME', $data));
            $this->setPhbFonMiddleName(CommonUtils::GetArrayValue('PHB_FON_MIDDLE_NAME', $data));
            $this->setPhbFonLastName1(CommonUtils::GetArrayValue('PHB_FON_LAST_NAME1', $data));
            $this->setPhbFonLastName2(CommonUtils::GetArrayValue('PHB_FON_LAST_NAME2', $data));
            $this->setPhbLastUpdateUtc(CommonUtils::GetArrayValue('PHB_LAST_UPDATE_UTC', $data));
            $this->setPhbExtAccessKey(CommonUtils::GetArrayValue('PHB_EXT_ACCESS_KEY', $data));
            $this->setPhbGiAllowPlaybackIntNumber(CommonUtils::GetArrayValue('PHB_GI_ALLOW_PLAYBACK_INT_NUMBER', $data));
            $this->setPhbGeAllowPlaybackIntNumber(CommonUtils::GetArrayValue('PHB_GE_ALLOW_PLAYBACK_INT_NUMBER', $data));
            $this->setPhbAllowHtdfExt(CommonUtils::GetArrayValue('PHB_ALLOW_HTDF_EXT', $data));
            $this->setPhbAllowHtdfInt(CommonUtils::GetArrayValue('PHB_ALLOW_HTDF_INT', $data));
            $this->setPhbAllowVozExt(CommonUtils::GetArrayValue('PHB_ALLOW_VOZ_EXT', $data));
            $this->setPhbAllowVozInt(CommonUtils::GetArrayValue('PHB_ALLOW_VOZ_INT', $data));
            $this->setPhbPlayMsgInfoTranf(CommonUtils::GetArrayValue('PHB_PLAY_MSG_INFO_TRANF', $data));
            $this->setPhbMsgSecPersonal(CommonUtils::GetArrayValue('PHB_MSG_SEC_PERSONAL', $data));
            $this->setPhbIsTransf(CommonUtils::GetArrayValue('PHB_IS_TRANSF', $data));

            $this->setTransferOptions(CommonUtils::GetArrayValue('PHB_TRANSFER_OPTIONS', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
            $this->setDepName(CommonUtils::GetArrayValue('DEP_NAME', $data));
            $this->setPhbGrammarOptions(CommonUtils::GetArrayValue('PHB_GRAMMAR_OPTIONS', $data));
            $this->setSecName(CommonUtils::GetArrayValue('SEC_NAME', $data));
        } else if ($data instanceof Persona) {
            $this->setPhbId($data->getPhbId());
            $this->setPhbDepId($data->getPhbDepId());
            $this->setPhbFirstName($data->getPhbFirstName());
            $this->setPhbMiddleName($data->getPhbMiddleName());
            $this->setPhbLastName1($data->getPhbLastName1());
            $this->setPhbLastName2($data->getPhbLastName2());
            $this->setPhbNickName($data->getPhbNickName());
            $this->setPhbEmail($data->getPhbEmail());
            $this->setPhbIsSec($data->getPhbIsSec());
            $this->setPhbSecId($data->getPhbSecId());
            $this->setPhbDaytimeNumber($data->getPhbDaytimeNumber());
            $this->setPhbNighttimeNumber($data->getPhbNighttimeNumber());
            $this->setPhbDaytimeCellular($data->getPhbDaytimeCellular());
            $this->setPhbNighttimeCellular($data->getPhbNighttimeCellular());
            $this->setPhbFlags($data->getPhbFlags());
            $this->setPhbIntGuideNumber($data->getPhbIntGuideNumber());
            $this->setPhbExtGuideNumber($data->getPhbExtGuideNumber());
            $this->setPhbFaxDaytime($data->getPhbFaxDaytime());
            $this->setPhbFaxNighttime($data->getPhbFaxNighttime());
            $this->setPhbDialpostNumberFaxDaytime($data->getPhbDialpostNumberFaxDaytime());
            $this->setPhbDialpostNumberFaxNighttime($data->getPhbDialpostNumberFaxNighttime());
            $this->setPhbVmaDaytime($data->getPhbVmaDaytime());
            $this->setPhbVmaNighttime($data->getPhbVmaNighttime());
            $this->setPhbDialpostNumberVmaDaytime($data->getPhbDialpostNumberVmaDaytime());
            $this->setPhbDialpostNumberVmaNighttime($data->getPhbDialpostNumberVmaNighttime());
            $this->setPhbGrammar($data->getPhbGrammar());
            $this->setPhbConfirmation($data->getPhbConfirmation());
            $this->setPhbFonFirstName($data->getPhbFonFirstName());
            $this->setPhbFonMiddleName($data->getPhbFonMiddleName());
            $this->setPhbFonLastName1($data->getPhbFonLastName1());
            $this->setPhbFonLastName2($data->getPhbFonLastName2());
            $this->setPhbLastUpdateUtc($data->getPhbLastUpdateUtc());
            $this->setPhbExtAccessKey($data->getPhbExtAccessKey());
            $this->setPhbGiAllowPlaybackIntNumber($data->getPhbGiAllowPlaybackIntNumber());
            $this->setPhbGeAllowPlaybackIntNumber($data->getPhbGeAllowPlaybackIntNumber());
            $this->setPhbAllowHtdfExt($data->getPhbAllowHtdfExt());
            $this->setPhbAllowHtdfInt($data->getPhbAllowHtdfInt());
            $this->setPhbAllowVozExt($data->getPhbAllowVozExt());
            $this->setPhbAllowVozInt($data->getPhbAllowVozInt());
            $this->setPhbPlayMsgInfoTranf($data->getPhbPlayMsgInfoTranf());
            $this->setPhbMsgSecPersonal($data->getPhbMsgSecPersonal());
            $this->setPhbIsTransf($data->getPhbIsTransf());

            $this->setDepName($data->getDepName());
            $this->setPhbGrammarOptions($data->getPhbGrammarOptions());
            $this->setSecName($data->getSecName());
        }
        // $this->phb_full_name = trim($this->getPhbFirstName() . " " . $this->getPhbMiddleName() . " " . $this->getPhbLastName1() . " " . $this->getPhbLastName2());
        $this->phb_full_name = trim($this->getPhbLastName1() . " " . $this->getPhbLastName2() . ", " . $this->getPhbFirstName() . " " . $this->getPhbMiddleName());
    }

    public function toArray()
    {
        $returnArray = [];
        foreach ($this as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $itemInArray) {
                    if (is_object($itemInArray)) {
                        $returnArray[$key][] = $itemInArray->toArray();
                    } else {
                        $returnArray[$key][] = $itemInArray;
                    }
                }
            } else {
                $returnArray[$key] = $value;
            }
        }
        return $returnArray;
    }

    /**
     * @return mixed
     */
    public function getPhbId()
    {
        return $this->phb_id;
    }

    /**
     * @param mixed $phb_id
     */
    public function setPhbId($phb_id)
    {
        $this->phb_id = $phb_id;
    }

    /**
     * @return mixed
     */
    public function getPhbDepId()
    {
        return $this->phb_dep_id;
    }

    /**
     * @param mixed $phb_dep_id
     */
    public function setPhbDepId($phb_dep_id)
    {
        $this->phb_dep_id = $phb_dep_id;
    }

    /**
     * @return mixed
     */
    public function getPhbFirstName()
    {
        return $this->phb_first_name;
    }

    /**
     * @param mixed $phb_first_name
     */
    public function setPhbFirstName($phb_first_name)
    {
        $this->phb_first_name = $phb_first_name;
    }

    /**
     * @return mixed
     */
    public function getPhbMiddleName()
    {
        return $this->phb_middle_name;
    }

    /**
     * @param mixed $phb_middle_name
     */
    public function setPhbMiddleName($phb_middle_name)
    {
        $this->phb_middle_name = $phb_middle_name;
    }

    /**
     * @return mixed
     */
    public function getPhbLastName1()
    {
        return $this->phb_last_name1;
    }

    /**
     * @param mixed $phb_last_name1
     */
    public function setPhbLastName1($phb_last_name1)
    {
        $this->phb_last_name1 = $phb_last_name1;
    }

    /**
     * @return mixed
     */
    public function getPhbLastName2()
    {
        return $this->phb_last_name2;
    }

    /**
     * @param mixed $phb_last_name2
     */
    public function setPhbLastName2($phb_last_name2)
    {
        $this->phb_last_name2 = $phb_last_name2;
    }

    /**
     * @return mixed
     */
    public function getPhbNickName()
    {
        return $this->phb_nick_name;
    }

    /**
     * @param mixed $phb_nick_name
     */
    public function setPhbNickName($phb_nick_name)
    {
        $this->phb_nick_name = $phb_nick_name;
    }

    /**
     * @return mixed
     */
    public function getPhbEmail()
    {
        return $this->phb_email;
    }

    /**
     * @param mixed $phb_email
     */
    public function setPhbEmail($phb_email)
    {
        $this->phb_email = $phb_email;
    }

    /**
     * @return mixed
     */
    public function getPhbIsSec()
    {
        return $this->phb_is_sec;
    }

    /**
     * @param mixed $phb_is_sec
     */
    public function setPhbIsSec($phb_is_sec)
    {
        $this->phb_is_sec = $phb_is_sec;
    }

    /**
     * @return mixed
     */
    public function getPhbSecId()
    {
        return $this->phb_sec_id;
    }

    /**
     * @param mixed $phb_sec_id
     */
    public function setPhbSecId($phb_sec_id)
    {
        $this->phb_sec_id = $phb_sec_id;
    }

    /**
     * @return mixed
     */
    public function getPhbDaytimeNumber()
    {
        return $this->phb_daytime_number;
    }

    /**
     * @param mixed $phb_daytime_number
     */
    public function setPhbDaytimeNumber($phb_daytime_number)
    {
        $this->phb_daytime_number = $phb_daytime_number;
    }

    /**
     * @return mixed
     */
    public function getPhbNighttimeNumber()
    {
        return $this->phb_nighttime_number;
    }

    /**
     * @param mixed $phb_nighttime_number
     */
    public function setPhbNighttimeNumber($phb_nighttime_number)
    {
        $this->phb_nighttime_number = $phb_nighttime_number;
    }

    /**
     * @return mixed
     */
    public function getPhbDaytimeCellular()
    {
        return $this->phb_daytime_cellular;
    }

    /**
     * @param mixed $phb_daytime_cellular
     */
    public function setPhbDaytimeCellular($phb_daytime_cellular)
    {
        $this->phb_daytime_cellular = $phb_daytime_cellular;
    }

    /**
     * @return mixed
     */
    public function getPhbNighttimeCellular()
    {
        return $this->phb_nighttime_cellular;
    }

    /**
     * @param mixed $phb_nighttime_cellular
     */
    public function setPhbNighttimeCellular($phb_nighttime_cellular)
    {
        $this->phb_nighttime_cellular = $phb_nighttime_cellular;
    }

    /**
     * @return mixed
     */
    public function getPhbFlags()
    {
        return $this->phb_flags;
    }

    /**
     * @param mixed $phb_flags
     */
    public function setPhbFlags($phb_flags)
    {
        $this->phb_flags = $phb_flags;
    }

    /**
     * @return mixed
     */
    public function getPhbIntGuideNumber()
    {
        return $this->phb_int_guide_number;
    }

    /**
     * @param mixed $phb_int_guide_number
     */
    public function setPhbIntGuideNumber($phb_int_guide_number)
    {
        $this->phb_int_guide_number = $phb_int_guide_number;
    }

    /**
     * @return mixed
     */
    public function getPhbExtGuideNumber()
    {
        return $this->phb_ext_guide_number;
    }

    /**
     * @param mixed $phb_ext_guide_number
     */
    public function setPhbExtGuideNumber($phb_ext_guide_number)
    {
        $this->phb_ext_guide_number = $phb_ext_guide_number;
    }

    /**
     * @return mixed
     */
    public function getPhbFaxDaytime()
    {
        return $this->phb_fax_daytime;
    }

    /**
     * @param mixed $phb_fax_daytime
     */
    public function setPhbFaxDaytime($phb_fax_daytime)
    {
        $this->phb_fax_daytime = $phb_fax_daytime;
    }

    /**
     * @return mixed
     */
    public function getPhbDialpostNumberFaxDaytime()
    {
        return $this->phb_dialpost_number_fax_daytime;
    }

    /**
     * @param mixed $phb_dialpost_number_fax_daytime
     */
    public function setPhbDialpostNumberFaxDaytime($phb_dialpost_number_fax_daytime)
    {
        $this->phb_dialpost_number_fax_daytime = $phb_dialpost_number_fax_daytime;
    }

    /**
     * @return mixed
     */
    public function getPhbFaxNighttime()
    {
        return $this->phb_fax_nighttime;
    }

    /**
     * @param mixed $phb_fax_nighttime
     */
    public function setPhbFaxNighttime($phb_fax_nighttime)
    {
        $this->phb_fax_nighttime = $phb_fax_nighttime;
    }

    /**
     * @return mixed
     */
    public function getPhbDialpostNumberFaxNighttime()
    {
        return $this->phb_dialpost_number_fax_nighttime;
    }

    /**
     * @param mixed $phb_dialpost_number_fax_nighttime
     */
    public function setPhbDialpostNumberFaxNighttime($phb_dialpost_number_fax_nighttime)
    {
        $this->phb_dialpost_number_fax_nighttime = $phb_dialpost_number_fax_nighttime;
    }

    /**
     * @return mixed
     */
    public function getPhbVmaDaytime()
    {
        return $this->phb_vma_daytime;
    }

    /**
     * @param mixed $phb_vma_daytime
     */
    public function setPhbVmaDaytime($phb_vma_daytime)
    {
        $this->phb_vma_daytime = $phb_vma_daytime;
    }

    /**
     * @return mixed
     */
    public function getPhbVmaNighttime()
    {
        return $this->phb_vma_nighttime;
    }

    /**
     * @param mixed $phb_vma_nighttime
     */
    public function setPhbVmaNighttime($phb_vma_nighttime)
    {
        $this->phb_vma_nighttime = $phb_vma_nighttime;
    }

    /**
     * @return mixed
     */
    public function getPhbDialpostNumberVmaDaytime()
    {
        return $this->phb_dialpost_number_vma_daytime;
    }

    /**
     * @param mixed $phb_dialpost_number_vma_daytime
     */
    public function setPhbDialpostNumberVmaDaytime($phb_dialpost_number_vma_daytime)
    {
        $this->phb_dialpost_number_vma_daytime = $phb_dialpost_number_vma_daytime;
    }

    /**
     * @return mixed
     */
    public function getPhbDialpostNumberVmaNighttime()
    {
        return $this->phb_dialpost_number_vma_nighttime;
    }

    /**
     * @param mixed $phb_dialpost_number_vma_nighttime
     */
    public function setPhbDialpostNumberVmaNighttime($phb_dialpost_number_vma_nighttime)
    {
        $this->phb_dialpost_number_vma_nighttime = $phb_dialpost_number_vma_nighttime;
    }

    /**
     * @return mixed
     */
    public function getPhbGrammar()
    {
        return $this->phb_grammar;
    }

    /**
     * @param mixed $phb_grammar
     */
    public function setPhbGrammar($phb_grammar)
    {
        $this->phb_grammar = $phb_grammar;
    }

    /**
     * @return mixed
     */
    public function getPhbConfirmation()
    {
        return $this->phb_confirmation;
    }

    /**
     * @param mixed $phb_confirmation
     */
    public function setPhbConfirmation($phb_confirmation)
    {
        $this->phb_confirmation = $phb_confirmation;
    }

    /**
     * @return mixed
     */
    public function getPhbFonFirstName()
    {
        return $this->phb_fon_first_name;
    }

    /**
     * @param mixed $phb_fon_first_name
     */
    public function setPhbFonFirstName($phb_fon_first_name)
    {
        $this->phb_fon_first_name = $phb_fon_first_name;
    }

    /**
     * @return mixed
     */
    public function getPhbFonMiddleName()
    {
        return $this->phb_fon_middle_name;
    }

    /**
     * @param mixed $phb_fon_middle_name
     */
    public function setPhbFonMiddleName($phb_fon_middle_name)
    {
        $this->phb_fon_middle_name = $phb_fon_middle_name;
    }

    /**
     * @return mixed
     */
    public function getPhbFonLastName1()
    {
        return $this->phb_fon_last_name1;
    }

    /**
     * @param mixed $phb_fon_last_name1
     */
    public function setPhbFonLastName1($phb_fon_last_name1)
    {
        $this->phb_fon_last_name1 = $phb_fon_last_name1;
    }

    /**
     * @return mixed
     */
    public function getPhbFonLastName2()
    {
        return $this->phb_fon_last_name2;
    }

    /**
     * @param mixed $phb_fon_last_name2
     */
    public function setPhbFonLastName2($phb_fon_last_name2)
    {
        $this->phb_fon_last_name2 = $phb_fon_last_name2;
    }

    /**
     * @return mixed
     */
    public function getPhbLastUpdateUtc()
    {
        return $this->phb_last_update_utc;
    }

    /**
     * @param mixed $phb_last_update_utc
     */
    public function setPhbLastUpdateUtc($phb_last_update_utc)
    {
        $this->phb_last_update_utc = $phb_last_update_utc;
    }

    /**
     * @return mixed
     */
    public function getPhbExtAccessKey()
    {
        return $this->phb_ext_access_key;
    }

    /**
     * @param mixed $phb_ext_access_key
     */
    public function setPhbExtAccessKey($phb_ext_access_key)
    {
        $this->phb_ext_access_key = $phb_ext_access_key;
    }

    /**
     * @return mixed
     */
    public function getPhbGiAllowPlaybackIntNumber()
    {
        return $this->phb_gi_allow_playback_int_number;
    }

    /**
     * @param mixed $phb_gi_allow_playback_int_number
     */
    public function setPhbGiAllowPlaybackIntNumber($phb_gi_allow_playback_int_number)
    {
        $this->phb_gi_allow_playback_int_number = $phb_gi_allow_playback_int_number;
    }

    /**
     * @return mixed
     */
    public function getPhbGeAllowPlaybackIntNumber()
    {
        return $this->phb_ge_allow_playback_int_number;
    }

    /**
     * @param mixed $phb_ge_allow_playback_int_number
     */
    public function setPhbGeAllowPlaybackIntNumber($phb_ge_allow_playback_int_number)
    {
        $this->phb_ge_allow_playback_int_number = $phb_ge_allow_playback_int_number;
    }

    /**
     * @return mixed
     */
    public function getTransferOptions()
    {
        return $this->transfer_options;
    }

    /**
     * @param mixed $transferOptions
     */
    public function setTransferOptions($transfer_options)
    {
        $this->transfer_options = $transfer_options;
    }

    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->COMPANY_ID;
    }

    /**
     * @param mixed $business_id
     */
    public function setBusinessId($business_id)
    {
        $this->COMPANY_ID = $business_id;
    }

    /**
     * @return mixed
     */
    public function getPhbFullName()
    {
        return $this->phb_full_name;
    }

    /**
     * @return mixed
     */
    public function getPhbAllowHtdfExt()
    {
        return $this->phb_allow_htdf_ext;
    }

    /**
     * @param mixed $phb_allow_htdf_ext
     */
    public function setPhbAllowHtdfExt($phb_allow_htdf_ext)
    {
        $this->phb_allow_htdf_ext = $phb_allow_htdf_ext;
    }

    /**
     * @return mixed
     */
    public function getPhbAllowHtdfInt()
    {
        return $this->phb_allow_htdf_int;
    }

    /**
     * @param mixed $phb_allow_htdf_int
     */
    public function setPhbAllowHtdfInt($phb_allow_htdf_int)
    {
        $this->phb_allow_htdf_int = $phb_allow_htdf_int;
    }

    /**
     * @return mixed
     */
    public function getPhbAllowVozExt()
    {
        return $this->phb_allow_voz_ext;
    }

    /**
     * @param mixed $phb_allow_voz_ext
     */
    public function setPhbAllowVozExt($phb_allow_voz_ext)
    {
        $this->phb_allow_voz_ext = $phb_allow_voz_ext;
    }

    /**
     * @return mixed
     */
    public function getPhbAllowVozInt()
    {
        return $this->phb_allow_voz_int;
    }

    /**
     * @param mixed $phb_allow_voz_int
     */
    public function setPhbAllowVozInt($phb_allow_voz_int)
    {
        $this->phb_allow_voz_int = $phb_allow_voz_int;
    }

    /**
     * @return mixed
     */
    public function getPhbPlayMsgInfoTranf()
    {
        return $this->phb_play_msg_info_tranf;
    }

    /**
     * @param mixed $phb_play_msg_info_tranf
     */
    public function setPhbPlayMsgInfoTranf($phb_play_msg_info_tranf)
    {
        $this->phb_play_msg_info_tranf = $phb_play_msg_info_tranf;
    }

    /**
     * @return mixed
     */
    public function getPhbMsgSecPersonal()
    {
        return $this->phb_msg_sec_personal;
    }

    /**
     * @param mixed $phb_msg_sec_personal
     */
    public function setPhbMsgSecPersonal($phb_msg_sec_personal)
    {
        $this->phb_msg_sec_personal = $phb_msg_sec_personal;
    }

    /**
     * @param mixed $phb_is_transf
     */
    public function setPhbIsTransf($phb_is_transf)
    {
        $this->phb_is_transf = $phb_is_transf;
    }

    /**
     * @return mixed
     */
    public function getPhbIsTransf()
    {
        return $this->phb_is_transf;
    }

    /**
     * @return mixed
     */
    public function getDepName()
    {
        return $this->dep_name;
    }

    /**
     * @param mixed $dep_name
     */
    public function setDepName($dep_name)
    {
        $this->dep_name = $dep_name;
    }

    /**
     * @return mixed
     */
    public function getPhbGrammarOptions()
    {
        return $this->phb_grammar_options;
    }

    /**
     * @param mixed $phb_grammar_options
     */
    public function setPhbGrammarOptions($phb_grammar_options)
    {
        $this->phb_grammar_options = $phb_grammar_options;
    }

    /**
     * @return mixed
     */
    public function getSecName()
    {
        return $this->sec_name;
    }

    /**
     * @param mixed $sec_name
     */
    public function setSecName($sec_name)
    {
        $this->sec_name = $sec_name;
    }

}