<?php

namespace model;

use \utils\CommonUtils;

class Estadistica {

    private $frd_id;
    private $dar_date;
    private $frd_description;
    private $dar_value;
    private $frd_unit_singular;
	private $frd_unit_plural;
	private $max_troncales_usadas;

	private $frd_unit;
	private $valor_total_en_el_dia;
	private $dar_date_value;
	private $existe_fdr_id_3000;

	function __construct($data = null)
    {
        if ($data == null) { return; }
        $this->setFrd_id(CommonUtils::GetArrayValue('FDR_ID', $data));
        $this->setDar_date(CommonUtils::GetArrayValue('DAR_DATE', $data));
        $this->setFrd_description(CommonUtils::GetArrayValue('FDR_DESCRIPTION', $data));
        $this->setDar_value(CommonUtils::GetArrayValue('DAR_VALUE', $data));
        $this->setFrd_unit_singular(CommonUtils::GetArrayValue('FDR_UNIT_SINGULAR', $data));
		$this->setFrd_unit_plural(CommonUtils::GetArrayValue('FDR_UNIT_PLURAL', $data));
		$this->setFrd_unit_plural(CommonUtils::GetArrayValue('FDR_UNIT_PLURAL', $data));
		$this->setMax_troncales_usadas(CommonUtils::GetArrayValue('MAX_TRONCALES_USADAS', $data));
		$this->setFrd_unit(CommonUtils::GetArrayValue('FDR_UNIT', $data));
		$this->setValorTotalEnElDia(CommonUtils::GetArrayValue('VALOR_TOTAL_EN_EL_DIA', $data));
		$this->setDar_date_value(CommonUtils::GetArrayValue('DAR_DATE_VALUE', $data));
		$this->setExiste_frd_id_3000(CommonUtils::GetArrayValue('EXISTE_FDR_ID_3000', $data));
	}

    public function getFrd_id(){
		return $this->frd_id;
	}

	public function setFrd_id($frd_id){
		$this->frd_id = $frd_id;
	}

	public function getDar_date(){
		return $this->dar_date;
	}

	public function setDar_date($dar_date){
		$this->dar_date = $dar_date;
	}

	public function getFrd_description(){
		return $this->frd_description;
	}

	public function setFrd_description($frd_description){
		$this->frd_description = $frd_description;
	}

	public function getDar_value(){
		return $this->dar_value;
	}

	public function setDar_value($dar_value){
		$this->dar_value = $dar_value;
	}

	public function getFrd_unit_singular(){
		return $this->frd_unit_singular;
	}

	public function setFrd_unit_singular($frd_unit_singular){
		$this->frd_unit_singular = $frd_unit_singular;
	}

	public function getFrd_unit_plural(){
		return $this->frd_unit_plural;
	}

	public function setFrd_unit_plural($frd_unit_plural){
		$this->frd_unit_plural = $frd_unit_plural;
    }
 
	public function getMax_troncales_usadas(){
		return $this->max_troncales_usadas;
	}

	public function setMax_troncales_usadas($max_troncales_usadas){
		$this->max_troncales_usadas = $max_troncales_usadas;
	}

	public function getFrd_unit(){
		return $this->frd_unit;
	}

	public function setFrd_unit($frd_unit){
		$this->frd_unit = $frd_unit;
	}

	public function getValorTotalEnElDia(){
		return $this->valor_total_en_el_dia;
	}

	public function setValorTotalEnElDia($valor_total_en_el_dia){
		$this->valor_total_en_el_dia = $valor_total_en_el_dia;
	}

	public function getDar_date_value(){
		return $this->dar_date_value;
	}

	public function setDar_date_value($dar_date_value){
		$this->dar_date_value = $dar_date_value;
	}

	public function getExiste_frd_id_3000(){
		return $this->existe_fdr_id_3000;
	}

	public function setExiste_frd_id_3000($existe_fdr_id_3000){
		$this->existe_fdr_id_3000 = $existe_fdr_id_3000;
	}

}
