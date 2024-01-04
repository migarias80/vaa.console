<?php

namespace serviceimpl;

use daoimpl\FuncionSQLDAO;
use dto\CheckArchivoDTO;
use model\Fax;
use service\ICheckService;
use daoimpl\DepartmentSQLDAO;
use daoimpl\PersonaSQLDAO;
use daoimpl\ParametroSQLDAO;
use daoimpl\EmpresaSQLDAO;

class CheckService implements ICheckService
{

    private $departmentSQLDAO;
    private $phbDAO;
    private $parametroSQLDAO;
    private $empresaSQLDAO;

    function __construct() {
        $this->departmentSQLDAO = new DepartmentSQLDAO();
        $this->phbDAO = new PersonaSQLDAO();
        $this->parametroSQLDAO = new ParametroSQLDAO();
        $this->empresaSQLDAO = new EmpresaSQLDAO();
    }

    function checkArchivosDepartamentos($idEmpresa) {
        $departamentos = $this->departmentSQLDAO->getDepartment($idEmpresa);
        if ($departamentos == null) {
            return [];
        }

        $directorio = $this->getDirectorio($idEmpresa);
        if ($directorio == null) {
            return null;
        }
        $directorio .= "\\DEPARTMENTS";

        $checkArchivosDTO = array();
        foreach ($departamentos as $departamento) {
            $nombreArchivo = $this->armarNombreArchivoDepartamento($departamento);

            if (!file_exists(utf8_decode($directorio . "\\" . $nombreArchivo))) {
                $checkArchivoDTO = new CheckArchivoDTO();
                $checkArchivoDTO->setArchivo($nombreArchivo);
                $checkArchivoDTO->setTipo(1);
                $checkArchivosDTO[] = $checkArchivoDTO;
            }
        }
        return $checkArchivosDTO;
    }

    function checkArchivosPersonas($idEmpresa) {
        $personas = $this->phbDAO->getPersona($idEmpresa);
        if ($personas == null) {
            return [];
        }

        $directorio = $this->getDirectorio($idEmpresa);
        if ($directorio == null) {
            return null;
        }
        $directorio .= "\\PEOPLE";

        $checkArchivosDTO = array();
        foreach ($personas as $persona) {
            // -- Persona
            $nombreArchivo = $this->armarNombreArchivoPersona($persona);

            if (!file_exists(utf8_decode($directorio . "\\" . $nombreArchivo))) {
                $checkArchivoDTO = new CheckArchivoDTO();
                $checkArchivoDTO->setArchivo($nombreArchivo);
                $checkArchivoDTO->setTipo(2);
                $checkArchivosDTO[] = $checkArchivoDTO;
            }

            // -- Asistente (Chequeado sobre la persona que lo tiene)
            if ($persona->getPhbSecId() != null) {
                $nombreArchivoSec = $this->armarNombreArchivoPersona($persona, true);

                if (!file_exists(utf8_decode($directorio . "\\" . $nombreArchivoSec))) {
                    $checkArchivoDTO = new CheckArchivoDTO();
                    $checkArchivoDTO->setArchivo($nombreArchivoSec);
                    $checkArchivoDTO->setTipo(3);
                    $checkArchivosDTO[] = $checkArchivoDTO;
                }
            }
        }
        return $checkArchivosDTO;
    }

    private function armarNombreArchivoDepartamento($departamento) {
        $dpto = str_replace(" ", "_", $departamento->getDepName());
        $dpto = str_replace(".", "", $dpto);
        $dpto = str_replace("'", "", $dpto);

        $nombreArchivo = "DEP_" . $dpto . ".VOX";

        return $nombreArchivo;
    }

    private function armarNombreArchivoPersona($persona, $esSecretaria=false) {
        $primerNombre = str_replace(" ", "_", $persona->getPhbFirstName());
        $segundoNombre = str_replace(" ", "_", $persona->getPhbMiddleName());
        $nombres = $primerNombre;
        if ($segundoNombre != "" && $segundoNombre != null) {
            $nombres .= "_" . $segundoNombre;
        }

        $primerApellido = str_replace(" ", "_", $persona->getPhbLastName1());
        $segundoApellido = str_replace(" ", "_", $persona->getPhbLastName2());
        $apellidos = $primerApellido;
        if ($segundoApellido != "" && $segundoApellido != null) {
            $apellidos .= "_" . $segundoApellido;
        }

        $nombres = str_replace(" ", "_", $nombres);
        $nombres = str_replace(".", "", $nombres);
        $nombres = str_replace("'", "", $nombres);
        $apellidos = str_replace(" ", "_", $apellidos);
        $apellidos = str_replace(".", "", $apellidos);
        $apellidos = str_replace("'", "", $apellidos);

        if ($esSecretaria) {
            $nombreArchivo = $nombres . "_" . $apellidos . "_secretary.VOX";
        } else {
            $nombreArchivo = $nombres . "_" . $apellidos . ".VOX";
        }
        
        return $nombreArchivo;
    }

    private function getDirectorio($idEmpresa) {
        $directorio = $this->parametroSQLDAO->getParametros(1, ["VOX_DIRECTORY"]);
        if ($directorio == null) {
            return null;
        }
        if (count($directorio) == 0) {
            return null;
        }
        $directorio = $directorio[0];
        $directorio = $directorio->getParValue();

        $empresa = $this->empresaSQLDAO->getEmpresa($idEmpresa);
        if ($empresa == null) {
            return null;
        }
        if (count($empresa) == 0) {
            return null;
        }
        $empresa = $empresa[0];
        
        $directorio .= "\\COM_" . $empresa->getUrlName();
        return $directorio;
    }

}