<?php

namespace dto;

use \model\Domain;
use \utils\CommonUtils;

class DomainDTO implements IGenericDTO {

	private $dom_id;
	private $dom_regex;
	private $dom_domain;
	private $dom_use_ani_ip_for_refer;
	private $business_id;

	function __construct(Domain $domain = null) {
		if ($domain == null) { return; }
		$this->setDom_id($domain->getDom_id());
        $this->setDom_regex($domain->getDom_regex());
		$this->setDom_domain($domain->getDom_domain());
		$this->setDom_use_ani_ip_for_refer($domain->getDom_use_ani_ip_for_refer());
        $this->setBusiness_id($domain->getBusiness_id());
    }

    public function constructFromArray($data) {
		$this->setDom_id(CommonUtils::GetArrayValue('DOM_ID', $data));
        $this->setDom_regex(CommonUtils::GetArrayValue('DOM_REGEX', $data));
		$this->setDom_domain(CommonUtils::GetArrayValue('DOM_DOMAIN', $data));
		$this->setDom_use_ani_ip_for_refer(CommonUtils::GetArrayValue('DOM_USE_ANI_IP_FOR_REFER', $data));
        $this->setBusiness_id(CommonUtils::GetArrayValue('COMPANY_ID', $data));
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
