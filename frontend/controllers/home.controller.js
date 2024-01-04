/**
 * HOME
 * Controller del home del cliente
 */
app.controller('HomeController', function($scope, $http, UtilsService, UsuarioFactory, $rootScope, UsuarioService, $filter, CONST, ParametroService, EmpresaService, PersonaService, DepartamentoService, DomainService) {

    // -- Configuracion incial de la pantalla
    $scope.puedeConfigurarParametros = false;
    $scope.puedeConfigurarParametrosDeLaEmpresa = false;
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Inicio", false);
        $rootScope.mostrarEmpresas = true;
        $scope.puedeConfigurarParametros = true;
    } else {
        UtilsService.getMenu("Inicio");
        $scope.puedeConfigurarParametrosDeLaEmpresa = true;
    }
    $rootScope.mostrarAdmUsuarios = false;
    if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value || UsuarioFactory.get().idProfile == CONST.PROFILES[1].value) {
        $rootScope.mostrarAdmUsuarios = true;
    }
    $scope.nombreEmpresa = UsuarioFactory.get().nombreEmpresa;
    $rootScope.nombreUsuario = UsuarioFactory.get().nombre;
    $rootScope.nombreUsuarioFull = UsuarioFactory.get().nombreFull;
    var auxProfile = $filter('filter')(CONST.PROFILES, {value: UsuarioFactory.get().idProfile})[0];
    $rootScope.nombrePerfil = auxProfile.profile.toLowerCase();
    $scope.habilitarActivarEmpresa = false;

    // -- Dominios
    $scope.gridOptions = {
        data: [],
        urlSync: false
    };

    // -- Rutas
    $scope.rutasMHC = [];

    // -- Get ultimo acceso
    UsuarioService.getLastAccess().then(function(response) {
        if (!angular.isUndefined(response.data.data)) {
            $scope.lastAccess = "Último acceso " + response.data.data.last_access;
        }
    });

    // -- Logo de la empresa
    if (UsuarioFactory.get().img != null) {
        $rootScope.setLogoEmpresa('assets/public/' + UsuarioFactory.get().img);
    }

    $scope.cargando = true;
    if (!$scope.puedeConfigurarParametros) {
        $scope.cargando = false;
    }

    // -- Solo si puede configurar parametros
    if ($scope.puedeConfigurarParametros) {
        
        // -- Se obtienen los datos de la empresa
        $scope.empresa = {
            id: UsuarioFactory.get().idEmpresa,
            dnis_regex: "",
            dnis_regex_ext: "",
            output_route: ""
        }
        EmpresaService.loadEmpresa().then(function(response){
            $scope.empresa.dnis_regex = response.data.data.dnis_regex;
            $scope.empresa.dnis_regex_ext = response.data.data.dnis_regex_ext;
            $scope.empresa.output_route = response.data.data.output_route;
            $scope.cfg_max_phone_books = response.data.data.cant_max_personas;
            $scope.cfg_max_departments = response.data.data.cant_max_departamentos;
            $scope.cfg_max_lines = response.data.data.cant_max_lines;
            $scope.cfg_contact = response.data.data.contacto;
            $scope.cfg_notes = response.data.data.notas;
            $scope.cfg_vaa_active = {
                par_value: response.data.data.vaa_active,
                par_value_boolean: (response.data.data.vaa_active == 1)? true : false
            }
            $scope.isExpresionRegularDNISValid();
            $scope.isExpresionRegularDNIS_ExtValid();

            if ($scope.cfg_max_phone_books != null && $scope.cfg_max_departments != null) {
                configurarCharts(true, true);
            }
            if (puedeSerHabilitado()) {
                $scope.habilitarActivarEmpresa = true;
            }
            
            // -- Se obtienen los dominios de la empresa
            obtenerDominios();

            // -- Se obtienen las rutas (mhc) de la empresa
            obtenerRutasMHC();

        }, function() { });
    }

    // -- Obtener rutas
    function obtenerRutasMHC() {
        $scope.cargando = true;
        $scope.rutasMHC = [];
        EmpresaService.getRutasMHCDeEmpresa().then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                $scope.rutasMHC.push({
                    value: value.ruta,
                    label: value.ruta
                });
            });
            if ($scope.rutasMHC === undefined) {
                $scope.rutasMHC = [];
            }
            $scope.cargando = false;
        }, function() {
            $scope.cargando = false;
        });
    }

    // -- Obtener dominios
    function obtenerDominios() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        DomainService.getDomain().then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                $scope.gridOptions.data.push(value);
            });
            $scope.cargando = false;
        }, function() {
            $scope.cargando = false;
        });
    }

    // -- Dominio seleccionado?
    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.dom_id == $scope.idSeleccionado) {
            return true;
        }
        return false;
    };

    // -- Click sobre el dominio
    $scope.idSeleccionado = undefined;
    $scope.itemSeleccionado = undefined;
    $scope.viewItem = function(item, forceView) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.dom_id && !forceView) {
                
            } else {
                $scope.idSeleccionado = item.dom_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, {dom_id: item.dom_id})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);
            }
        }
    };

    // -- Context menu del dominio
    $scope.menuOptions = [
        {
            text: 'Editar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.editarDominio($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.eliminarDominio($itemScope.item);
            }
        }
    ];

    // -- Crear dominio
    $scope.crearDominio = function() {
        UtilsService.showDialog("views/domain.html?_=" + new Date().getTime(), "DomainController").then(function(answer) {
            obtenerDominios();
        }, function() {

        });
    };

    // -- Editar dominio
    $scope.editarDominio = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/domain.html?_=" + new Date().getTime(), "DomainController", item).then(function(answer) {
                obtenerDominios();
            }, function() {

            });
        }
    };

    // -- Eliminar dominio
    $scope.eliminarDominio = function(item) {
        if (!angular.isUndefined(item)) {
            if (item.dom_regex == "DEFAULT") {
                UtilsService.showToast({delay: 5000, text: "No es posible eliminar el Dial Plan por defecto"});
                return;
            }
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.dom_domain + "?").then(function() {
                DomainService.eliminar(item.dom_id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.dom_domain + " fue eliminado correctamente"});
                        obtenerDominios();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.dom_domain});
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                });

            });
        }
    };

    // -- Modificacion del logo en el evento change
    $scope.file_data = null;
    $( document ).ready(function() {
        $('#fileInput').change(function(){
            $scope.file_data = $("#fileInput").prop("files")[0];
        });
    });

    // -- Guardar empresa
    $scope.guardarEmpresa = function() {
        if (!UsuarioFactory.get().isAdmin) {
            return;
        }

        if (!$scope.isExpresionRegularDNISValid()) {
            UtilsService.showToast({delay: 5000, text: "La expresión regular para activación por DNIS de la guía interna es incorrecta"});
            return;
        }
        if (!$scope.isExpresionRegularDNIS_ExtValid()) {
            UtilsService.showToast({delay: 5000, text: "La expresión regular para activación por DNIS de la guía externa es incorrecta"});
            return;
        }
        if (isNaN($scope.cfg_max_phone_books)) {
            $scope.cfg_max_phone_books = "";
            UtilsService.showToast({delay: 5000, text: "La cantidad máxima de personas es incorrecta"});
            return;
        }
        if (isNaN($scope.cfg_max_departments)) {
            $scope.cfg_max_departments = "";
            UtilsService.showToast({delay: 5000, text: "La cantidad máxima de departamentos es incorrecta"});
            return;
        }
        if (isNaN($scope.cfg_max_lines)) {
            $scope.cfg_max_lines = "";
            UtilsService.showToast({delay: 5000, text: "La cantidad máxima de líneas entrantes es incorrecta"});
            return;
        }

        if ($scope.cfg_vaa_active.par_value_boolean) {
            $scope.cfg_vaa_active.par_value = 1;
        } else {
            $scope.cfg_vaa_active.par_value = 0;
        }
        if (!puedeSerHabilitado() && $scope.cfg_vaa_active.par_value == 1) {
            $scope.cfg_vaa_active.par_value = 0;
            $scope.cfg_vaa_active.par_value_boolean = false;
            UtilsService.showToast({delay: 5000, text: "La empresa no puede ser habilitada debido a que no se encuentra completamente configurada"});
            return;
        }

        var data = {
            id: UsuarioFactory.get().idEmpresa,
            dnis_regex: $scope.empresa.dnis_regex,
            dnis_regex_ext: $scope.empresa.dnis_regex_ext,
            output_route: $scope.empresa.output_route,
            cant_max_personas: $scope.cfg_max_phone_books,
            cant_max_departamentos: $scope.cfg_max_departments,
            cant_max_lines: $scope.cfg_max_lines,
            contacto: $scope.cfg_contact,
            notas: $scope.cfg_notes,
            vaa_active: $scope.cfg_vaa_active.par_value
        }
        $scope.cargando = true;
        EmpresaService.setConfiguracionGeneral(data).then(function(response){
            if (!angular.isUndefined($scope.file_data) && $scope.file_data != null) {
                var form_data = new FormData();
                form_data.append("bid", UsuarioFactory.get().idEmpresa);
                form_data.append("img", $scope.file_data);
                EmpresaService.uploadImage(form_data, function(response){
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        EmpresaService.setImage({id: UsuarioFactory.get().idEmpresa, img: response.url});
                        $('#logoEmpresa').attr('src', 'assets/public/' + response.url);

                        configurarCharts(true, true);
                        $scope.cargando = false;
                        UtilsService.showToast({delay: 5000, text: "La configuración de la empresa fue guardada correctamente"});
                    } else {
                        configurarCharts(true, true);
                        $scope.cargando = false;
                        UtilsService.showToast({delay: 5000, text: "No es posible actualizar el logo de la empresa"});
                    }
                });
            } else {
                configurarCharts(true, true);
                $scope.cargando = false;
                UtilsService.showToast({delay: 5000, text: "La configuración de la empresa fue guardada correctamente"});
            }
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            $scope.cargando = false;
        });
    }

    // -- Autoguardado de la activacion de la empresa
    $scope.modificarVAAActivado = function() {
        if (!UsuarioFactory.get().isAdmin) {
            return;
        }
        if (!puedeSerHabilitado() && $scope.cfg_vaa_active.par_value == 1) {
            $scope.cfg_vaa_active.par_value = 0;
            $scope.cfg_vaa_active.par_value_boolean = false;
            UtilsService.showToast({delay: 5000, text: "La empresa no puede ser habilitada debido a que no se encuentra completamente configurada"});
            return;
        }
        var data = {
            id: UsuarioFactory.get().idEmpresa
        }
        if ($scope.cfg_vaa_active.par_value_boolean) {
            $scope.cfg_vaa_active.par_value = 1;
        } else {
            $scope.cfg_vaa_active.par_value = 0;
        }
        $scope.cargando = true;
        if ($scope.cfg_vaa_active.par_value_boolean) {
            EmpresaService.setEnabled(data).then(function() {
                $scope.cargando = false;
                UtilsService.showToast({delay: 5000, text: "VAA habilitado para la empresa " + $scope.nombreEmpresa});
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                $scope.cargando = false;
            });
        } else {
            EmpresaService.setDisabled(data).then(function() {
                $scope.cargando = false;
                UtilsService.showToast({delay: 5000, text: "VAA deshabilitado para la empresa " + $scope.nombreEmpresa});
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                $scope.cargando = false;
            });
        }
    };

    // -- Validacion de DNIS regex para guia interna
    $scope.expresionRegularDNISValid = false;
    $scope.isExpresionRegularDNISValid = function() {
        try {
            new RegExp($scope.empresa.dnis_regex);
            $scope.expresionRegularDNISValid = true;
            return true;
        } catch(e) {
            $scope.expresionRegularDNISValid = false;
            return false;
        }
    }

    // -- Validacion de DNIS regex para guia externa
    $scope.expresionRegularDNIS_ExtValid = false;
    $scope.isExpresionRegularDNIS_ExtValid = function() {
        try {
            new RegExp($scope.empresa.dnis_regex_ext);
            $scope.expresionRegularDNIS_ExtValid = true;
            return true;
        } catch(e) {
            $scope.expresionRegularDNIS_ExtValid = false;
            return false;
        }
    }

    // -- Verifica si la empresa puede ser o no habilitada, en caso negativo la deshabilita
    /* function puedeSerHabilitado() {
        if ($scope.empresa.dnis_regex == "" || 
            $scope.empresa.dnis_regex_ext == "" || 
            $scope.empresa.output_route == "" || 
            $scope.cfg_max_phone_books == "" || 
            $scope.cfg_max_departments == "" || 
            $scope.cfg_max_lines == "" || 
            $scope.empresa.dnis_regex == null || 
            $scope.empresa.dnis_regex_ext == null || 
            $scope.empresa.output_route == null || 
            $scope.cfg_max_phone_books == null || 
            $scope.cfg_max_departments == null || 
            $scope.cfg_max_lines == null
        ) {
            $scope.habilitarActivarEmpresa = false;
            if (!$scope.cfg_vaa_active.par_value_boolean) {
                return false;
            }
            var data = {
                id: UsuarioFactory.get().idEmpresa
            }
            $scope.cfg_vaa_active.par_value = 0;
            $scope.cfg_vaa_active.par_value_boolean = false;
            EmpresaService.setDisabled(data).then(function() {
                return false;
            });
        }
        $scope.habilitarActivarEmpresa = true;
        return true;
    } */

    // -- Evaluacion de activacion de empresainterna
    function puedeSerHabilitado() {
        if ($scope.empresa.dnis_regex == "" || 
            $scope.empresa.dnis_regex_ext == "" || 
            $scope.empresa.output_route == "" || 
            $scope.cfg_max_phone_books == "" || 
            $scope.cfg_max_departments == "" || 
            $scope.cfg_max_lines == "" || 
            $scope.empresa.dnis_regex == null || 
            $scope.empresa.dnis_regex_ext == null || 
            $scope.empresa.output_route == null || 
            $scope.cfg_max_phone_books == null || 
            $scope.cfg_max_departments == null || 
            $scope.cfg_max_lines == null
        ) {
            return false;
        }
        return true;
    }

    // -- Evaluacion de activacion de empresa desde el frontend, en los eventos blur
    $scope.evalHabilitarEmpresa = function() {
        if (!puedeSerHabilitado()) {
            $scope.habilitarActivarEmpresa = false;
            $scope.cfg_vaa_active.par_value = 0;
            $scope.cfg_vaa_active.par_value_boolean = false;
        } else {
            $scope.habilitarActivarEmpresa = true;
        }
    }

    // -- Configuracion de chart
    function configurarCharts(departamentos, personas) {
        if (departamentos && personas) {
            configurarChartDepartamento(configurarChartPersona);
        } else if (departamentos) {
            configurarChartDepartamento();
        } else if (personas) {
            configurarChartPersona()
        }
    }

    // -- Chart personas
    function configurarChartDepartamento(next) {
        PersonaService.getCantidadDePersonas().then(function(response){
            $scope.personas_labels = ["En uso", "Disponibles"];
            $scope.personas_data = [response.data.data, $scope.cfg_max_phone_books - response.data.data];
            $scope.personas_options = {
                title: {
                    display: true,
                    text: 'Personas',
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
            if (next !== undefined) {
                next();
            }
        }, function() { });
    }

    // -- Chart departamentos
    function configurarChartPersona(next) {
        DepartamentoService.getCantidadDeDepartamentos().then(function(response){
            $scope.departamentos_labels = ["En uso", "Disponibles"];
            $scope.departamentos_data = [response.data.data, $scope.cfg_max_departments - response.data.data];
            $scope.departamentos_options = {
                title: {
                    display: true,
                    text: 'Departamentos',
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
            if (next !== undefined) {
                next();
            }
        }, function() { });
    }

    // -- Obtengo parametros usuales para tenerlos listos al abrir un departamento o una persona
    ParametroService.getOpcionesDeConfirmacion().then(function (response) { }, function() {
        ParametroService.getOpcionesDeGramatica().then(function (response) { }, function() { });
    });

});