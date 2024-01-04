/**
 * HOME EDS
 * Controller del home de EDS
 */
app.controller('HomeEDSController', function($scope, $http, UtilsService, EmpresaService, $location, UsuarioFactory, $rootScope, UsuarioService, ParametroService, CONST, $filter) {

    // -- Configuracion incial de la pantalla
    UtilsService.processingStage();
    UtilsService.getMenuEDS("Empresas", true);
    $rootScope.mostrarEmpresas = true;
    $rootScope.mostrarAdmUsuarios = false;
    $rootScope.nombreUsuario = UsuarioFactory.get().nombre;
    $rootScope.nombreUsuarioFull = UsuarioFactory.get().nombreFull;
    if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value || UsuarioFactory.get().idProfile == CONST.PROFILES[1].value) {
        $rootScope.mostrarAdmUsuarios = true;
    }

    // -- Get empresas
    $scope.cargando = true;
    $scope.empresas = [];
    function obtenerEmpresas() {
        $scope.cargando = true;
        $scope.empresas = [];
        EmpresaService.getEmpresa().then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                if (value.id != 1) {
                    if (!$.isNumeric(value.cant_max_lines)) {
                        value.cant_max_lines = 0;
                    }
                    $scope.empresas.push(value);
                }
            });

            // -- Get parametros de configuracion global
            ParametroService.getParametros({ parametros: ['CANT_MAX_LINES_GLOBAL', 'VOX_DIRECTORY', 'MHC_FILE'] }).then(function(response){
                $scope.cargando = false;
                angular.forEach(response.data.data, function(value, key) {
                    if (value.par_name === 'CANT_MAX_LINES_GLOBAL') {
                        $scope.cfg_cant_max_lines_global = value;
                    } else if (value.par_name === 'VOX_DIRECTORY') {
                        $scope.vox_directory = value;
                    } else if (value.par_name === 'MHC_FILE') {
                        $scope.mhc_file = value;
                    } 
                });
                configurarCharts();
            }, function() {
                $scope.cargando = false;
            });
        });
    }

    // -- Obtener ultimo acceso y luego las empresas
    UsuarioService.getLastAccess().then(function(response) {
        if (!angular.isUndefined(response.data.data)) {
            $scope.lastAccess = "Último acceso " + response.data.data.last_access;
        }
        obtenerEmpresas();
    });

    // -- Logo de la empresa
    $rootScope.resetLogoEmpresa();

    // -- Tomar el control de una empresa
    $scope.tomarControl = function(empresa) {
        UsuarioFactory.setEmpresa(empresa.name, empresa.id, empresa.img, empresa.url_name);
        $rootScope.setLogoEmpresa(empresa.img);
        var dataLastAccess = { COMPANY_ID: UsuarioFactory.get().idEmpresa };
        UsuarioService.updateLastAccess(dataLastAccess).then(function() {
            UtilsService.showToast({delay: 5000, text: "Tomando el control de " + empresa.name});
            $location.path('/home');
        });
    };

    // -- Crear una empresa
    $scope.crearEmpresa = function() {
        UtilsService.showDialog("views/empresa.html?_=" + new Date().getTime(), "EmpresaController").then(function(answer) {
            obtenerEmpresas();
        }, function() {

        });
    };

    // -- Editar una empresa
    $scope.editarEmpresa = function(empresa) {
        UtilsService.showDialog("views/empresa.html?_=" + new Date().getTime(), "EmpresaController", empresa).then(function(answer) {
            obtenerEmpresas();
        }, function() {

        });
    };

    // -- Eliminar una empresa
    $scope.eliminarEmpresa = function(empresa) {
        if (!angular.isUndefined(empresa)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + empresa.name + ", junto con sus personas, departamentos, faxes, voice mails y configuración general?").then(function() {
                EmpresaService.eliminar(empresa.id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: empresa.name + " fue eliminado correctamente"});
                        obtenerEmpresas();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + empresa.name});
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                });
            });
        }
    }

    // -- Modificar parametros de configuracion global
    $scope.modificarConfiguracionGlobal = function() {
        validConfiguracion(function() {
            doModificarConfiguracionGlobal();
        });
    };

    function validConfiguracion(callback) {
        callback();
    }

    // -- Evaluacion de DNIS

    $scope.gridOptions = {
        data: null,
        urlSync: false,
        sort: {
            predicate: 'name',
            direction: 'asc'
        }
    };
    $scope.evaluarDNIS = function() {
        if (UtilsService.isEmpty($scope.dnis_regex) || $scope.dnis_regex == "") {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar un DNIS para evaluar"});
            return;
        }
        $scope.cargando = true;
        $scope.gridOptions.data = null;
        $scope.nombreEmpresaEvaluarDNIS = "";
        EmpresaService.evaluarDNIS($scope.dnis_regex).then(function(response){
            $scope.gridOptions.data = [];
            var guia = "";
            angular.forEach(response.data.data, function(value, key) {
                value.guia = "";
                if (value.dnis_regex == 1) {
                    value.guia = "Guía Interna";
                }
                if (value.dnis_regex_ext == 1) {
                    value.guia = "Guía Externa";
                }
                value.regex = value.output_route;

                $scope.gridOptions.data.push(value);
            });
            if ($scope.gridOptions.data.length == 0) {
                
            } else if ($scope.gridOptions.data.length > 1) {
                
            } else if ($scope.gridOptions.data.length == 1) {
                var regex = $scope.gridOptions.data[0].regex;
                $scope.nombreEmpresaEvaluarDNIS = $scope.gridOptions.data[0].name;
                if ($scope.gridOptions.data[0].dnis_regex == 1) {
                    $scope.nombreEmpresaEvaluarDNIS += ". Guía Interna. Regex: " + regex;
                }
                if ($scope.gridOptions.data[0].dnis_regex_ext == 1) {
                    $scope.nombreEmpresaEvaluarDNIS += ". Guía Externa. Regex: " + regex;
                }
            }
            $scope.cargando = false;
        }, function() {
    
        });
    }

    // -- Accion de Modificar parametros de configuracion global
    function doModificarConfiguracionGlobal() {
        $scope.cargando = true;
        var data = {parametros:[]};
        data.parametros.push($scope.cfg_cant_max_lines_global);
        data.parametros.push($scope.vox_directory);
        data.parametros.push($scope.mhc_file);
        ParametroService.modificarParametros(data).then(function(response){
            if (response.data.code == 1) {
                configurarCharts();
                UtilsService.showToast({delay: 5000, text: "Configuración general guardada correctamente"});
            } else if (response.data.code == 2) {
                UtilsService.showToast({delay: 5000, text: response.data.message});
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar la configuración general"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            $scope.cargando = false;
        });
    }

    function configurarCharts() {
        // -- Configuracion de chart lineas globales
        $scope.lines_labels = ["Asignadas", "Disponibles"];

        var acumLines = 0;
        angular.forEach($scope.empresas, function(value, key) {
            if (value.vaa_active == 1 && $.isNumeric(value.cant_max_lines)) {
                acumLines += parseInt(value.cant_max_lines);
            }
        });

        $scope.porcentajeExcedenteLineasEntrantes = 0;
        var limiteGrafico = $scope.cfg_cant_max_lines_global.par_value - acumLines;
        if (acumLines > $scope.cfg_cant_max_lines_global.par_value) {
            limiteGrafico = 0;
            $scope.porcentajeExcedenteLineasEntrantes = ((acumLines - $scope.cfg_cant_max_lines_global.par_value) * 100) / $scope.cfg_cant_max_lines_global.par_value;
            $scope.porcentajeExcedenteLineasEntrantes = Math.round($scope.porcentajeExcedenteLineasEntrantes * 100) / 100;
        }

        $scope.lines_data = [acumLines, limiteGrafico];
        $scope.lines_options = {
            title: {
                display: true,
                text: 'Total de Líneas Asignadas',
                fontColor: '#757575',
                fontSize: 12
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    generateLabels: function(chart) {
                        return UtilsService.generateChartLabel(chart);
                    }
                }
            }
        };

        // -- Configuracion de chart detalle por empresa
        var CANT_MAX_EMPRESAS_EN_GRAFICO_LINEAS = 5;
        $scope.detail_labels = [];
        $scope.detail_data = [];
        var detail_label_resto = "Resto";
        var detail_data_resto = 0;
        var empresasOrdenadasPorCantidadDeLineas = [];
        angular.forEach($scope.empresas, function(value, key) {
            value.cant_max_lines = parseInt(value.cant_max_lines);
            empresasOrdenadasPorCantidadDeLineas.push(value);
        });
        var empresasOrdenadasPorCantidadDeLineas = $filter('orderBy')(empresasOrdenadasPorCantidadDeLineas, 'cant_max_lines', true);
        for (var i in empresasOrdenadasPorCantidadDeLineas) {
            var value = empresasOrdenadasPorCantidadDeLineas[i];
            if (value.vaa_active == 1 && $.isNumeric(value.cant_max_lines)) {
                if ($scope.detail_data.length == CANT_MAX_EMPRESAS_EN_GRAFICO_LINEAS) {
                    detail_data_resto += value.cant_max_lines;
                } else {
                    $scope.detail_labels.push(value.name);
                    $scope.detail_data.push(value.cant_max_lines);
                }
            }
        }
        if (detail_data_resto > 0) {
            $scope.detail_labels.push(detail_label_resto);
            $scope.detail_data.push(detail_data_resto);
        }
        $scope.detail_options = {
            title: {
                display: true,
                text: 'Líneas Asignadas por Empresa',
                fontColor: '#757575',
                fontSize: 12
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    generateLabels: function(chart) {
                        return UtilsService.generateChartLabel(chart);
                    }
                }
            }
        };

        $scope.cargando = false;
    }

});