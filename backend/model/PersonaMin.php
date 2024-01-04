<?php

namespace model;

use \dto\PersonaDTO;
use \utils\CommonUtils;

class PersonaMin
{
    private $phb_id;
    private $phb_dep_id;
    private $phb_first_name;
    private $phb_middle_name;
    private $phb_last_name1;
    private $phb_last_name2;
    private $phb_nick_name;
    private $phb_email;
    private $phb_daytime_number;
    private $phb_nighttime_number;
    private $phb_daytime_cellular;
    private $phb_nighttime_cellular;

    private $dep_name;
    private $sec_name;

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
            $this->setPhbDaytimeNumber(CommonUtils::GetArrayValue('PHB_DAYTIME_NUMBER', $data));
            $this->setPhbNighttimeNumber(CommonUtils::GetArrayValue('PHB_NIGHTTIME_NUMBER', $data));
            $this->setPhbDaytimeCellular(CommonUtils::GetArrayValue('PHB_DAYTIME_CELLULAR', $data));
            $this->setPhbNighttimeCellular(CommonUtils::GetArrayValue('PHB_NIGHTTIME_CELLULAR', $data));

            $this->setDepName(CommonUtils::GetArrayValue('DEP_NAME', $data));
            $this->setSecName(CommonUtils::GetArrayValue('SEC_NAME', $data));
        } else if ($data instanceof PersonaDTO) {
            $this->setPhbId($data->getPhbId());
            $this->setPhbDepId($data->getPhbDepId());
            $this->setPhbFirstName($data->getPhbFirstName());
            $this->setPhbMiddleName($data->getPhbMiddleName());
            $this->setPhbLastName1($data->getPhbLastName1());
            $this->setPhbLastName2($data->getPhbLastName2());
            $this->setPhbNickName($data->getPhbNickName());
            $this->setPhbEmail($data->getPhbEmail());
            $this->setPhbDaytimeNumber($data->getPhbDaytimeNumber());
            $this->setPhbNighttimeNumber($data->getPhbNighttimeNumber());
            $this->setPhbDaytimeCellular($data->getPhbDaytimeCellular());
            $this->setPhbNighttimeCellular($data->getPhbNighttimeCellular());

            $this->setDepName($data->getDepName());
            $this->setSecName($data->getSecName());
        }
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

    public function getSecName()
    {
        return $this->sec_name;
    }

    public function setSecName($sec_name)
    {
        $this->sec_name = $sec_name;
    }

}