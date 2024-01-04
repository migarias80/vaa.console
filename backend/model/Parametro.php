<?php

namespace model;

use dto\ParametroDTO;
use \utils\CommonUtils;

class Parametro
{

    private $par_name;
    private $par_description;
    private $par_type;
    private $par_value;
    private $par_last_update_utc;
    private $business_id;
	private $COMPANY_ID;

    // private $par_order;
    // private $par_type_order;
    // private $par_datatype;
    // private $par_updateable;
    // private $par_regexp_validation;
    // private $par_query_validation;
    // private $par_app_name;
    // private $query_id;

    function __construct($data = null)
    {
        if ($data == null) { return; }
        if (is_array($data)) {
            $this->setParName(CommonUtils::GetArrayValue('PAR_NAME', $data));
            $this->setParDescription(CommonUtils::GetArrayValue('PAR_DESCRIPTION', $data));
            $this->setParType(CommonUtils::GetArrayValue('PAR_TYPE', $data));
            $this->setParValue(CommonUtils::GetArrayValue('PAR_VALUE', $data));
            $this->setParLastUpdateUtc(CommonUtils::GetArrayValue('PAR_LAST_UPDATE_UTC', $data));
            $this->setBusinessId(CommonUtils::GetArrayValue('COMPANY_ID', $data));

            // $this->setParOrder(CommonUtils::GetArrayValue('PAR_ORDER', $data));
            // $this->setParTypeOrder(CommonUtils::GetArrayValue('PAR_TYPE_ORDER', $data));
            // $this->setParDatatype(CommonUtils::GetArrayValue('PAR_DATATYPE', $data));
            // $this->setParUpdateable(CommonUtils::GetArrayValue('PAR_UPDATEABLE', $data));
            // $this->setParRegexpValidation(CommonUtils::GetArrayValue('PAR_REGEXP_VALIDATION', $data));
            // $this->setParQueryValidation(CommonUtils::GetArrayValue('PAR_QUERY_VALIDATION', $data));
            // $this->setParAppName(CommonUtils::GetArrayValue('PAR_APP_NAME', $data));
            // $this->setQueryId(CommonUtils::GetArrayValue('QUERY_ID', $data));
        } else if ($data instanceof ParametroDTO) {
            $this->setParName($data->getParName());
            $this->setParDescription($data->getParDescription());
            $this->setParType($data->getParType());
            $this->setParValue($data->getParValue());
            $this->setParLastUpdateUtc($data->getParLastUpdateUtc());
            $this->setBusinessId($data->getBusinessId());

            // $this->setParOrder($data->getParOrder());
            // $this->setParTypeOrder($data->getParTypeOrder());
            // $this->setParDatatype($data->getParDatatype());
            // $this->setParUpdateable($data->getParUpdateable());
            // $this->setParRegexpValidation($data->getParRegexpValidation());
            // $this->setParQueryValidation($data->getParQueryValidation());
            // $this->setParAppName($data->getParAppName());            
            // $this->setQueryId($data->getQueryId());
        }
    }

    /**
     * @return mixed
     */
    public function getParName()
    {
        return $this->par_name;
    }

    /**
     * @param mixed $par_name
     */
    public function setParName($par_name)
    {
        $this->par_name = $par_name;
    }

    /**
     * @return mixed
     */
    public function getParDescription()
    {
        return $this->par_description;
    }

    /**
     * @param mixed $par_description
     */
    public function setParDescription($par_description)
    {
        $this->par_description = $par_description;
    }

    /**
     * @return mixed
     */
    public function getParOrder()
    {
        return $this->par_order;
    }

    /**
     * @param mixed $par_order
     */
    public function setParOrder($par_order)
    {
        $this->par_order = $par_order;
    }

    /**
     * @return mixed
     */
    public function getParType()
    {
        return $this->par_type;
    }

    /**
     * @param mixed $par_type
     */
    public function setParType($par_type)
    {
        $this->par_type = $par_type;
    }

    /**
     * @return mixed
     */
    public function getParValue()
    {
        return $this->par_value;
    }

    /**
     * @param mixed $par_value
     */
    public function setParValue($par_value)
    {
        $this->par_value = $par_value;
    }

    /**
     * @return mixed
     */
    public function getParTypeOrder()
    {
        return $this->par_type_order;
    }

    /**
     * @param mixed $par_type_order
     */
    public function setParTypeOrder($par_type_order)
    {
        $this->par_type_order = $par_type_order;
    }

    /**
     * @return mixed
     */
    public function getParDatatype()
    {
        return $this->par_datatype;
    }

    /**
     * @param mixed $par_datatype
     */
    public function setParDatatype($par_datatype)
    {
        $this->par_datatype = $par_datatype;
    }

    /**
     * @return mixed
     */
    public function getParUpdateable()
    {
        return $this->par_updateable;
    }

    /**
     * @param mixed $par_updateable
     */
    public function setParUpdateable($par_updateable)
    {
        $this->par_updateable = $par_updateable;
    }

    /**
     * @return mixed
     */
    public function getParRegexpValidation()
    {
        return $this->par_regexp_validation;
    }

    /**
     * @param mixed $par_regexp_validation
     */
    public function setParRegexpValidation($par_regexp_validation)
    {
        $this->par_regexp_validation = $par_regexp_validation;
    }

    /**
     * @return mixed
     */
    public function getParQueryValidation()
    {
        return $this->par_query_validation;
    }

    /**
     * @param mixed $par_query_validation
     */
    public function setParQueryValidation($par_query_validation)
    {
        $this->par_query_validation = $par_query_validation;
    }

    /**
     * @return mixed
     */
    public function getParLastUpdateUtc()
    {
        return $this->par_last_update_utc;
    }

    /**
     * @param mixed $par_last_update_utc
     */
    public function setParLastUpdateUtc($par_last_update_utc)
    {
        $this->par_last_update_utc = $par_last_update_utc;
    }

    /**
     * @return mixed
     */
    public function getParAppName()
    {
        return $this->par_app_name;
    }

    /**
     * @param mixed $par_app_name
     */
    public function setParAppName($par_app_name)
    {
        $this->par_app_name = $par_app_name;
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
    public function getQueryId()
    {
        return $this->query_id;
    }

    /**
     * @param mixed $query_id
     */
    public function setQueryId($query_id)
    {
        $this->query_id = $query_id;
    }

}