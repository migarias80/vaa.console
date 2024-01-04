<?php

namespace service;

interface ICheckService
{
    public function checkArchivosDepartamentos($idEmpresa);
    public function checkArchivosPersonas($idEmpresa);
}