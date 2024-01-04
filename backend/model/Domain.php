<?php

namespace model;

use \utils\CommonUtils;

class Domain {

	private $dom_id;
	private $dom_regex;
	private $dom_domain;
	private $dom_use_ani_ip_for_refer;
	private $business_id;

	function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setDom_id(CommonUtils::GetArrayValue('DPL_ID', $data));
        $this->setDom_regex(CommonUtils::GetArrayValue('DPL_REGEX', $data));
		$this->setDom_domain(CommonUtils::GetArrayValue('DPL_IP_DOMAIN', $data));
		$this->setDom_use_ani_ip_for_refer(CommonUtils::GetArrayValue('DPL_USE_ANI_IP_FOR_REFER', $data));
        $this->setBusiness_id(CommonUtils::GetArrayValue('COMPANY_ID', $data));
	}

	public function getDom_id() {
		return $this->dom_id;
	}

	public function setDom_id($dom_id) {
		$this->dom_id = $dom_id;
	}

	public function getDom_regex() {
		return $this->dom_regex;
	}

	public function setDom_regex($dom_regex) {
		$this->dom_regex = $dom_regex;
	}


	public function getDom_domain() {
		return $this->dom_domain;
	}

	public function setDom_domain($dom_domain) {
		$this->dom_domain = $dom_domain;
	}
	
	public function getBusiness_id() {
		return $this->business_id;
	}

	public function setBusiness_id($business_id) {
		$this->business_id = $business_id;
	}

	public function getDom_use_ani_ip_for_refer() {
		return $this->dom_use_ani_ip_for_refer;
	}

	public function setDom_use_ani_ip_for_refer($dom_use_ani_ip_for_refer) {
		$this->dom_use_ani_ip_for_refer = $dom_use_ani_ip_for_refer;
	}

}
