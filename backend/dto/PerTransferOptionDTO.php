<?php

namespace dto;

use model\PerTransferOption;
use \utils\CommonUtils;

class PerTransferOptionDTO implements IGenericDTO
{

    private $tap_phb_id;
    private $tap_int_guide;
    private $tap_daytime;
    private $tap_order;
    private $tap_busy;
    private $tap_origin_number;
    private $tap_tao_id;
    private $tap_phb_transfer_type;
    private $tap_transf_phb_id;

    function __construct(PerTransferOption $perTransferOption = null)
    {
        if ($perTransferOption == null) { return; }
        $this->setTapPhbId($perTransferOption->getTapPhbId());
        $this->setTapIntGuide($perTransferOption->getTapIntGuide());
        $this->setTapDaytime($perTransferOption->getTapDaytime());
        $this->setTapOrder($perTransferOption->getTapOrder());
        $this->setTapBusy($perTransferOption->getTapBusy());
        $this->setTapOriginNumber($perTransferOption->getTapOriginNumber());
        $this->setTapTaoId($perTransferOption->getTapTaoId());
        $this->setTapPhbTransferType($perTransferOption->getTapPhbTransferType());
        $this->setTapTransfPhbId($perTransferOption->getTapTransfPhbId());
    }

    public function constructFromArray($data)
    {
        $this->setTapPhbId(CommonUtils::GetArrayValue('TAP_PHB_ID', $data));
        $this->setTapIntGuide(CommonUtils::GetArrayValue('TAP_INT_GUIDE', $data));
        $this->setTapDaytime(CommonUtils::GetArrayValue('TAP_DAYTIME', $data));
        $this->setTapOrder(CommonUtils::GetArrayValue('TAP_ORDER', $data));
        $this->setTapBusy(CommonUtils::GetArrayValue('TAP_BUSY', $data));
        $this->setTapOriginNumber(CommonUtils::GetArrayValue('TAP_ORIGIN_NUMBER', $data));
        $this->setTapTaoId(CommonUtils::GetArrayValue('TAP_TAO_ID', $data));
        $this->setTapPhbTransferType(CommonUtils::GetArrayValue('TAP_PHB_TRANSFER_TYPE', $data));
        $this->setTapTransfPhbId(CommonUtils::GetArrayValue('TAP_TRANSF_PHB_ID', $data));
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
    public function getTapPhbId()
    {
        return $this->tap_phb_id;
    }

    /**
     * @param mixed $tap_phb_id
     */
    public function setTapPhbId($tap_phb_id)
    {
        $this->tap_phb_id = $tap_phb_id;
    }

    /**
     * @return mixed
     */
    public function getTapIntGuide()
    {
        return $this->tap_int_guide;
    }

    /**
     * @param mixed $tap_int_guide
     */
    public function setTapIntGuide($tap_int_guide)
    {
        $this->tap_int_guide = $tap_int_guide;
    }

    /**
     * @return mixed
     */
    public function getTapDaytime()
    {
        return $this->tap_daytime;
    }

    /**
     * @param mixed $tap_daytime
     */
    public function setTapDaytime($tap_daytime)
    {
        $this->tap_daytime = $tap_daytime;
    }

    /**
     * @return mixed
     */
    public function getTapOrder()
    {
        return $this->tap_order;
    }

    /**
     * @param mixed $tap_order
     */
    public function setTapOrder($tap_order)
    {
        $this->tap_order = $tap_order;
    }

    /**
     * @return mixed
     */
    public function getTapBusy()
    {
        return $this->tap_busy;
    }

    /**
     * @param mixed $tap_busy
     */
    public function setTapBusy($tap_busy)
    {
        $this->tap_busy = $tap_busy;
    }

    /**
     * @return mixed
     */
    public function getTapOriginNumber()
    {
        return $this->tap_origin_number;
    }

    /**
     * @param mixed $tap_origin_number
     */
    public function setTapOriginNumber($tap_origin_number)
    {
        $this->tap_origin_number = $tap_origin_number;
    }

    /**
     * @return mixed
     */
    public function getTapTaoId()
    {
        return $this->tap_tao_id;
    }

    /**
     * @param mixed $tap_tao_id
     */
    public function setTapTaoId($tap_tao_id)
    {
        $this->tap_tao_id = $tap_tao_id;
    }

    /**
     * @return mixed
     */
    public function getTapPhbTransferType()
    {
        return $this->tap_phb_transfer_type;
    }

    /**
     * @param mixed $tap_phb_transfer_type
     */
    public function setTapPhbTransferType($tap_phb_transfer_type)
    {
        $this->tap_phb_transfer_type = $tap_phb_transfer_type;
    }

    /**
     * @return mixed
     */
    public function getTapTransfPhbId()
    {
        return $this->tap_transf_phb_id;
    }

    /**
     * @param mixed $tap_phb_id
     */
    public function setTapTransfPhbId($tap_transf_phb_id)
    {
        $this->tap_transf_phb_id = $tap_transf_phb_id;
    }

}