<?php

namespace dto;

use \model\Department;
use \utils\CommonUtils;

class DepartmentDTO implements IGenericDTO
{
    private $dep_id;
    private $dep_name;
    private $dep_email;
    private $dep_daytime_number;
    private $dep_nighttime_number;
    private $dep_daytime_cellular;
    private $dep_nighttime_cellular;
    private $dep_flags;
    private $dep_int_guide_number;
    private $dep_ext_guide_number;
    private $dep_fax_daytime;
    private $dep_fax_nighttime;
    private $dep_dialpost_number_fax_daytime;
    private $dep_dialpost_number_fax_nighttime;
    private $dep_vma_daytime;
    private $dep_vma_nighttime;
    private $dep_dialpost_number_vma_daytime;
    private $dep_dialpost_number_vma_nighttime;
    private $dep_confirmation;
    private $dep_fon_name;
    private $dep_last_update_utc;
    private $dep_gi_allow_playback_int_number;
    private $dep_ge_allow_playback_int_number;
    private $business_id;
    private $dep_allow_htdf_ext; // Flag
    private $dep_allow_htdf_int; // Flag
    private $dep_allow_voz_ext; // Flag
    private $dep_allow_voz_int; // Flag
    private $dep_play_msg_info_tranf; // Flag

    private $transfer_options;
	private $COMPANY_ID;

    function __construct($data = null) {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setDepId(CommonUtils::GetArrayValue('DEP_ID', $data));
            $this->setDepName(CommonUtils::GetArrayValue('DEP_NAME', $data));
            $this->setDepEmail(CommonUtils::GetArrayValue('DEP_EMAIL', $data));
            $this->setDepDaytimeNumber(CommonUtils::GetArrayValue('DEP_DAYTIME_NUMBER', $data));
            $this->setDepNighttimeNumber(CommonUtils::GetArrayValue('DEP_NIGHTTIME_NUMBER', $data));
            $this->setDepDaytimeCellular(CommonUtils::GetArrayValue('DEP_DAYTIME_CELLULAR', $data));
            $this->setDepNighttimeCellular(CommonUtils::GetArrayValue('DEP_NIGHTTIME_CELLULAR', $data));
            $this->setDepFlags(CommonUtils::GetArrayValue('DEP_FLAGS', $data));
            $this->setDepIntGuideNumber(CommonUtils::GetArrayValue('DEP_INT_GUIDE_NUMBER', $data));
            $this->setDepExtGuideNumber(CommonUtils::GetArrayValue('DEP_EXT_GUIDE_NUMBER', $data));
            $this->setDepFaxDaytime(CommonUtils::GetArrayValue('DEP_FAX_DAYTIME', $data));
            $this->setDepFaxNighttime(CommonUtils::GetArrayValue('DEP_FAX_NIGHTTIME', $data));
            $this->setDepDialpostNumberFaxDaytime(CommonUtils::GetArrayValue('DEP_DIALPOST_NUMBER_FAX_DAYTIME', $data));
            $this->setDepDialpostNumberFaxNighttime(CommonUtils::GetArrayValue('DEP_DIALPOST_NUMBER_FAX_NIGHTTIME', $data));
            $this->setDepVmaDaytime(CommonUtils::GetArrayValue('DEP_VMA_DAYTIME', $data));
            $this->setDepVmaNighttime(CommonUtils::GetArrayValue('DEP_VMA_NIGHTTIME', $data));
            $this->setDepDialpostNumberVmaDaytime(CommonUtils::GetArrayValue('DEP_DIALPOST_NUMBER_VMA_DAYTIME', $data));
            $this->setDepDialpostNumberVmaNighttime(CommonUtils::GetArrayValue('DEP_DIALPOST_NUMBER_VMA_NIGHTTIME', $data));
            $this->setDepConfirmation(CommonUtils::GetArrayValue('DEP_CONFIRMATION', $data));
            $this->setDepFonName(CommonUtils::GetArrayValue('DEP_FON_NAME', $data));
            $this->setDepLastUpdateUtc(CommonUtils::GetArrayValue('DEP_LAST_UPDATE_UTC', $data));
            $this->setDepGiAllowPlaybackIntNumber(CommonUtils::GetArrayValue('DEP_GI_ALLOW_PLAYBACK_INT_NUMBER', $data));
            $this->setDepGeAllowPlaybackIntNumber(CommonUtils::GetArrayValue('DEP_GE_ALLOW_PLAYBACK_INT_NUMBER', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));
            $this->setDepAllowHtdfExt(CommonUtils::GetArrayValue('DEP_ALLOW_HTDF_EXT', $data));
            $this->setDepAllowHtdfInt(CommonUtils::GetArrayValue('DEP_ALLOW_HTDF_INT', $data));
            $this->setDepAllowVozExt(CommonUtils::GetArrayValue('DEP_ALLOW_VOZ_EXT', $data));
            $this->setDepAllowVozInt(CommonUtils::GetArrayValue('DEP_ALLOW_VOZ_INT', $data));
            $this->setDepPlayMsgInfoTranf(CommonUtils::GetArrayValue('DEP_PLAY_MSG_INFO_TRANF', $data));

            $this->setTransferOptions(CommonUtils::GetArrayValue('DEP_TRANSFER_OPTIONS', $data));
        } else if ($data instanceof Department) {
            $this->setDepId($data->getDepId());
            $this->setDepName($data->getDepName());
            $this->setDepEmail($data->getDepEmail());
            $this->setDepDaytimeNumber($data->getDepDaytimeNumber());
            $this->setDepNighttimeNumber($data->getDepNighttimeNumber());
            $this->setDepDaytimeCellular($data->getDepDaytimeCellular());
            $this->setDepNighttimeCellular($data->getDepNighttimeCellular());
            $this->setDepFlags($data->getDepFlags());
            $this->setDepIntGuideNumber($data->getDepIntGuideNumber());
            $this->setDepExtGuideNumber($data->getDepExtGuideNumber());
            $this->setDepFaxDaytime($data->getDepFaxDaytime());
            $this->setDepFaxNighttime($data->getDepFaxNighttime());
            $this->setDepDialpostNumberFaxDaytime($data->getDepDialpostNumberFaxDaytime());
            $this->setDepDialpostNumberFaxNighttime($data->getDepDialpostNumberFaxNighttime());
            $this->setDepVmaDaytime($data->getDepVmaDaytime());
            $this->setDepVmaNighttime($data->getDepVmaNighttime());
            $this->setDepDialpostNumberVmaDaytime($data->getDepDialpostNumberVmaDaytime());
            $this->setDepDialpostNumberVmaNighttime($data->getDepDialpostNumberVmaNighttime());
            $this->setDepConfirmation($data->getDepConfirmation());
            $this->setDepFonName($data->getDepFonName());
            $this->setDepLastUpdateUtc($data->getDepLastUpdateUtc());
            $this->setDepGiAllowPlaybackIntNumber($data->getDepGiAllowPlaybackIntNumber());
            $this->setDepGeAllowPlaybackIntNumber($data->getDepGeAllowPlaybackIntNumber());
            $this->setBusinessId($data->getBusinessId());
            $this->setDepAllowHtdfExt($data->getDepAllowHtdfExt());
            $this->setDepAllowHtdfInt($data->getDepAllowHtdfInt());
            $this->setDepAllowVozExt($data->getDepAllowVozExt());
            $this->setDepAllowVozInt($data->getDepAllowVozInt());
            $this->setDepPlayMsgInfoTranf($data->getDepPlayMsgInfoTranf());
        }
    }

    public function toArray() {
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
    public function getDepId()
    {
        return $this->dep_id;
    }

    /**
     * @param mixed $dep_id
     */
    public function setDepId($dep_id)
    {
        $this->dep_id = $dep_id;
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
    public function getDepEmail()
    {
        return $this->dep_email;
    }

    /**
     * @param mixed $dep_email
     */
    public function setDepEmail($dep_email)
    {
        $this->dep_email = $dep_email;
    }

    /**
     * @return mixed
     */
    public function getDepDaytimeNumber()
    {
        return $this->dep_daytime_number;
    }

    /**
     * @param mixed $dep_daytime_number
     */
    public function setDepDaytimeNumber($dep_daytime_number)
    {
        $this->dep_daytime_number = $dep_daytime_number;
    }

    /**
     * @return mixed
     */
    public function getDepNighttimeNumber()
    {
        return $this->dep_nighttime_number;
    }

    /**
     * @param mixed $dep_nighttime_number
     */
    public function setDepNighttimeNumber($dep_nighttime_number)
    {
        $this->dep_nighttime_number = $dep_nighttime_number;
    }

    /**
     * @return mixed
     */
    public function getDepDaytimeCellular()
    {
        return $this->dep_daytime_cellular;
    }

    /**
     * @param mixed $dep_daytime_cellular
     */
    public function setDepDaytimeCellular($dep_daytime_cellular)
    {
        $this->dep_daytime_cellular = $dep_daytime_cellular;
    }

    /**
     * @return mixed
     */
    public function getDepNighttimeCellular()
    {
        return $this->dep_nighttime_cellular;
    }

    /**
     * @param mixed $dep_nighttime_cellular
     */
    public function setDepNighttimeCellular($dep_nighttime_cellular)
    {
        $this->dep_nighttime_cellular = $dep_nighttime_cellular;
    }

    /**
     * @return mixed
     */
    public function getDepFlags()
    {
        return $this->dep_flags;
    }

    /**
     * @param mixed $dep_flags
     */
    public function setDepFlags($dep_flags)
    {
        $this->dep_flags = $dep_flags;
    }

    /**
     * @return mixed
     */
    public function getDepIntGuideNumber()
    {
        return $this->dep_int_guide_number;
    }

    /**
     * @param mixed $dep_int_guide_number
     */
    public function setDepIntGuideNumber($dep_int_guide_number)
    {
        $this->dep_int_guide_number = $dep_int_guide_number;
    }

    /**
     * @return mixed
     */
    public function getDepExtGuideNumber()
    {
        return $this->dep_ext_guide_number;
    }

    /**
     * @param mixed $dep_ext_guide_number
     */
    public function setDepExtGuideNumber($dep_ext_guide_number)
    {
        $this->dep_ext_guide_number = $dep_ext_guide_number;
    }

    /**
     * @return mixed
     */
    public function getDepFaxDaytime()
    {
        return $this->dep_fax_daytime;
    }

    /**
     * @param mixed $dep_fax_daytime
     */
    public function setDepFaxDaytime($dep_fax_daytime)
    {
        $this->dep_fax_daytime = $dep_fax_daytime;
    }

    /**
     * @return mixed
     */
    public function getDepFaxNighttime()
    {
        return $this->dep_fax_nighttime;
    }

    /**
     * @param mixed $dep_fax_nighttime
     */
    public function setDepFaxNighttime($dep_fax_nighttime)
    {
        $this->dep_fax_nighttime = $dep_fax_nighttime;
    }

    /**
     * @return mixed
     */
    public function getDepDialpostNumberFaxDaytime()
    {
        return $this->dep_dialpost_number_fax_daytime;
    }

    /**
     * @param mixed $dep_dialpost_number_fax_daytime
     */
    public function setDepDialpostNumberFaxDaytime($dep_dialpost_number_fax_daytime)
    {
        $this->dep_dialpost_number_fax_daytime = $dep_dialpost_number_fax_daytime;
    }

    /**
     * @return mixed
     */
    public function getDepDialpostNumberFaxNighttime()
    {
        return $this->dep_dialpost_number_fax_nighttime;
    }

    /**
     * @param mixed $dep_dialpost_number_fax_nighttime
     */
    public function setDepDialpostNumberFaxNighttime($dep_dialpost_number_fax_nighttime)
    {
        $this->dep_dialpost_number_fax_nighttime = $dep_dialpost_number_fax_nighttime;
    }

    /**
     * @return mixed
     */
    public function getDepVmaDaytime()
    {
        return $this->dep_vma_daytime;
    }

    /**
     * @param mixed $dep_vma_daytime
     */
    public function setDepVmaDaytime($dep_vma_daytime)
    {
        $this->dep_vma_daytime = $dep_vma_daytime;
    }

    /**
     * @return mixed
     */
    public function getDepVmaNighttime()
    {
        return $this->dep_vma_nighttime;
    }

    /**
     * @param mixed $dep_vma_nighttime
     */
    public function setDepVmaNighttime($dep_vma_nighttime)
    {
        $this->dep_vma_nighttime = $dep_vma_nighttime;
    }

    /**
     * @return mixed
     */
    public function getDepDialpostNumberVmaDaytime()
    {
        return $this->dep_dialpost_number_vma_daytime;
    }

    /**
     * @param mixed $dep_dialpost_number_vma_daytime
     */
    public function setDepDialpostNumberVmaDaytime($dep_dialpost_number_vma_daytime)
    {
        $this->dep_dialpost_number_vma_daytime = $dep_dialpost_number_vma_daytime;
    }

    /**
     * @return mixed
     */
    public function getDepDialpostNumberVmaNighttime()
    {
        return $this->dep_dialpost_number_vma_nighttime;
    }

    /**
     * @param mixed $dep_dialpost_number_vma_nighttime
     */
    public function setDepDialpostNumberVmaNighttime($dep_dialpost_number_vma_nighttime)
    {
        $this->dep_dialpost_number_vma_nighttime = $dep_dialpost_number_vma_nighttime;
    }

    /**
     * @return mixed
     */
    public function getDepConfirmation()
    {
        return $this->dep_confirmation;
    }

    /**
     * @param mixed $dep_confirmation
     */
    public function setDepConfirmation($dep_confirmation)
    {
        $this->dep_confirmation = $dep_confirmation;
    }

    /**
     * @return mixed
     */
    public function getDepFonName()
    {
        return $this->dep_fon_name;
    }

    /**
     * @param mixed $dep_fon_name
     */
    public function setDepFonName($dep_fon_name)
    {
        $this->dep_fon_name = $dep_fon_name;
    }

    /**
     * @return mixed
     */
    public function getDepLastUpdateUtc()
    {
        return $this->dep_last_update_utc;
    }

    /**
     * @param mixed $dep_last_update_utc
     */
    public function setDepLastUpdateUtc($dep_last_update_utc)
    {
        $this->dep_last_update_utc = $dep_last_update_utc;
    }

    /**
     * @return mixed
     */
    public function getDepGiAllowPlaybackIntNumber()
    {
        return $this->dep_gi_allow_playback_int_number;
    }

    /**
     * @param mixed $dep_gi_allow_playback_int_number
     */
    public function setDepGiAllowPlaybackIntNumber($dep_gi_allow_playback_int_number)
    {
        $this->dep_gi_allow_playback_int_number = $dep_gi_allow_playback_int_number;
    }

    /**
     * @return mixed
     */
    public function getDepGeAllowPlaybackIntNumber()
    {
        return $this->dep_ge_allow_playback_int_number;
    }

    /**
     * @param mixed $dep_ge_allow_playback_int_number
     */
    public function setDepGeAllowPlaybackIntNumber($dep_ge_allow_playback_int_number)
    {
        $this->dep_ge_allow_playback_int_number = $dep_ge_allow_playback_int_number;
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
    public function getTransferOptions()
    {
        return $this->transfer_options;
    }

    /**
     * @param mixed $transfer_options
     */
    public function setTransferOptions($transfer_options)
    {
        $this->transfer_options = $transfer_options;
    }

    /**
     * @return mixed
     */
    public function getDepAllowHtdfExt()
    {
        return $this->dep_allow_htdf_ext;
    }

    /**
     * @param mixed $dep_allow_htdf_ext
     */
    public function setDepAllowHtdfExt($dep_allow_htdf_ext)
    {
        $this->dep_allow_htdf_ext = $dep_allow_htdf_ext;
    }

    /**
     * @return mixed
     */
    public function getDepAllowHtdfInt()
    {
        return $this->dep_allow_htdf_int;
    }

    /**
     * @param mixed $dep_allow_htdf_int
     */
    public function setDepAllowHtdfInt($dep_allow_htdf_int)
    {
        $this->dep_allow_htdf_int = $dep_allow_htdf_int;
    }

    /**
     * @return mixed
     */
    public function getDepAllowVozExt()
    {
        return $this->dep_allow_voz_ext;
    }

    /**
     * @param mixed $dep_allow_voz_ext
     */
    public function setDepAllowVozExt($dep_allow_voz_ext)
    {
        $this->dep_allow_voz_ext = $dep_allow_voz_ext;
    }

    /**
     * @return mixed
     */
    public function getDepAllowVozInt()
    {
        return $this->dep_allow_voz_int;
    }

    /**
     * @param mixed $dep_allow_voz_int
     */
    public function setDepAllowVozInt($dep_allow_voz_int)
    {
        $this->dep_allow_voz_int = $dep_allow_voz_int;
    }

    /**
     * @return mixed
     */
    public function getDepPlayMsgInfoTranf()
    {
        return $this->dep_play_msg_info_tranf;
    }

    /**
     * @param mixed $dep_play_msg_info_tranf
     */
    public function setDepPlayMsgInfoTranf($dep_play_msg_info_tranf)
    {
        $this->dep_play_msg_info_tranf = $dep_play_msg_info_tranf;
    }
}