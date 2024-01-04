<?php

namespace model;

use \utils\CommonUtils;

class DepTransferOption
{

    private $tad_dep_id;
    private $tad_int_guide;
    private $tad_daytime;
    private $tad_order;
    private $tad_busy;
    private $tad_origin_number;
    private $tad_tao_id;
    private $tad_dep_transfer_type;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setTadDepId(CommonUtils::GetArrayValue('TAD_DEP_ID', $data));
        $this->setTadIntGuide(CommonUtils::GetArrayValue('TAD_INT_GUIDE', $data));
        $this->setTadDaytime(CommonUtils::GetArrayValue('TAD_DAYTIME', $data));
        $this->setTadOrder(CommonUtils::GetArrayValue('TAD_ORDER', $data));
        $this->setTadBusy(CommonUtils::GetArrayValue('TAD_BUSY', $data));
        $this->setTadOriginNumber(CommonUtils::GetArrayValue('TAD_ORIGIN_NUMBER', $data));
        $this->setTadTaoId(CommonUtils::GetArrayValue('TAD_TAO_ID', $data));
        $this->setTadDepTransferType(CommonUtils::GetArrayValue('TAD_DEP_TRANSFER_TYPE', $data));
    }

    /**
     * @return mixed
     */
    public function getTadDepId()
    {
        return $this->tad_dep_id;
    }

    /**
     * @param mixed $tad_dep_id
     */
    public function setTadDepId($tad_dep_id)
    {
        $this->tad_dep_id = $tad_dep_id;
    }

    /**
     * @return mixed
     */
    public function getTadIntGuide()
    {
        return $this->tad_int_guide;
    }

    /**
     * @param mixed $tad_int_guide
     */
    public function setTadIntGuide($tad_int_guide)
    {
        $this->tad_int_guide = $tad_int_guide;
    }

    /**
     * @return mixed
     */
    public function getTadDaytime()
    {
        return $this->tad_daytime;
    }

    /**
     * @param mixed $tad_daytime
     */
    public function setTadDaytime($tad_daytime)
    {
        $this->tad_daytime = $tad_daytime;
    }

    /**
     * @return mixed
     */
    public function getTadOrder()
    {
        return $this->tad_order;
    }

    /**
     * @param mixed $tad_order
     */
    public function setTadOrder($tad_order)
    {
        $this->tad_order = $tad_order;
    }

    /**
     * @return mixed
     */
    public function getTadBusy()
    {
        return $this->tad_busy;
    }

    /**
     * @param mixed $tad_busy
     */
    public function setTadBusy($tad_busy)
    {
        $this->tad_busy = $tad_busy;
    }

    /**
     * @return mixed
     */
    public function getTadOriginNumber()
    {
        return $this->tad_origin_number;
    }

    /**
     * @param mixed $tad_origin_number
     */
    public function setTadOriginNumber($tad_origin_number)
    {
        $this->tad_origin_number = $tad_origin_number;
    }

    /**
     * @return mixed
     */
    public function getTadTaoId()
    {
        return $this->tad_tao_id;
    }

    /**
     * @param mixed $tad_tao_id
     */
    public function setTadTaoId($tad_tao_id)
    {
        $this->tad_tao_id = $tad_tao_id;
    }

    /**
     * @return mixed
     */
    public function getTadDepTransferType()
    {
        return $this->tad_dep_transfer_type;
    }

    /**
     * @param mixed $tad_dep_transfer_type
     */
    public function setTadDepTransferType($tad_dep_transfer_type)
    {
        $this->tad_dep_transfer_type = $tad_dep_transfer_type;
    }

}