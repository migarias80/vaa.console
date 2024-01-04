<?php

namespace serviceimpl;

use daoimpl\DomainSQLDAO;
use dto\DomainDTO;
use model\Domain;
use service\IDomainService;
use \Exception;

class DomainService implements IDomainService
{

    private $domainDAO;

    function __construct() {
        $this->domainDAO = new DomainSQLDAO();
    }

    function getDomain($idEmpresa, $id=null, $regex=null) {
        $domains = $this->domainDAO->getDomain($idEmpresa, $id, $regex);
        if ($domains == null) {
            return null;
        }

        $domainsDTO = array();
        foreach ($domains as $domain) {
            $domainsDTO[] = new DomainDTO($domain);
        }
        return $domainsDTO;
    }

    function guardar(DomainDTO $domainDTO) {
        $domain = new Domain();
        $domain->setDom_id($domainDTO->getDom_id());
        $domain->setDom_regex($domainDTO->getDom_regex());
        $domain->setDom_domain($domainDTO->getDom_domain());
        $domain->setDom_use_ani_ip_for_refer($domainDTO->getDom_use_ani_ip_for_refer());
        $domain->setBusiness_id($domainDTO->getBusiness_id());

        if ($domain->getDom_use_ani_ip_for_refer() == 1) {
            $domain->setDom_domain("");
        }

        $domains = $this->domainDAO->getDomain($domainDTO->getBusiness_id(), null, "DEFAULT");
        if ($domains == null || count($domains) == 0) {
            throw new Exception("Dial Plan por defecto no encontrado");
        }
        $domains = $domains[0];
        if ($domains->getDom_id() == $domain->getDom_id()) {
            if ($domains->getDom_regex() != "DEFAULT") {
                throw new Exception("No es posible cambiar el Regex del Dial Plan por defecto");
            }
        } else {
            if (strtoupper($domain->getDom_regex()) == "DEFAULT") {
                throw new Exception("Ya existe un Dial Plan por defecto");
            }
        }
        
        $this->domainDAO->guardar($domain);
    }

    public function eliminar($idEmpresa, $id)
    {
        $domains = $this->domainDAO->getDomain($idEmpresa, $id);
        if ($domains == null || count($domains) == 0) {
            throw new Exception("Dial Plan no encontrado");
        }
        $domains = $domains[0];
        if (strtoupper($domains->getDom_regex())  == "DEFAULT") {
            throw new Exception("No es posible eliminar el Dial Plan por defecto");
        }
        $this->domainDAO->eliminar($idEmpresa, $id);
    }
}