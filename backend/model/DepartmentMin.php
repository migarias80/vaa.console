<?php

namespace model;

use dto\DepartmentMinDTO;
use \utils\CommonUtils;

class DepartmentMin
{
    private $dep_id;
    private $dep_name;
    private $dep_email;
    private $dep_daytime_number;
    private $dep_nighttime_number;
    private $dep_daytime_cellular;
    private $dep_nighttime_cellular;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setDepId(CommonUtils::GetArrayValue('DEP_ID', $data));
            $this->setDepName(CommonUtils::GetArrayValue('DEP_NAME', $data));
            $this->setDepEmail(CommonUtils::GetArrayValue('DEP_EMAIL', $data));
            $this->setDepDaytimeNumber(CommonUtils::GetArrayValue('DEP_DAYTIME_NUMBER', $data));
            $this->setDepNighttimeNumber(CommonUtils::GetArrayValue('DEP_NIGHTTIME_NUMBER', $data));
            $this->setDepDaytimeCellular(CommonUtils::GetArrayValue('DEP_DAYTIME_CELLULAR', $data));
            $this->setDepNighttimeCellular(CommonUtils::GetArrayValue('DEP_NIGHTTIME_CELLULAR', $data));
        } else if ($data instanceof DepartmentMinDTO) {
            $this->setDepId($data->getDepId());
            $this->setDepName($data->getDepName());
            $this->setDepEmail($data->getDepEmail());
            $this->setDepDaytimeNumber($data->getDepDaytimeNumber());
            $this->setDepNighttimeNumber($data->getDepNighttimeNumber());
            $this->setDepDaytimeCellular($data->getDepDaytimeCellular());
            $this->setDepNighttimeCellular($data->getDepNighttimeCellular());
        }
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

}

