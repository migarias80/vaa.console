/**
 * PARAMETROS
 * Controller de los parametros
 */
app.controller('ParametrosController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, ParametroService, CONST, $filter, $q, FaxService, VoiceMailService, EmpresaService) {

    // -- Armado de entorno
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Parametros", false, (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)));
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Parametros");
    }
    $rootScope.mostrarAdmUsuarios = false;
    if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value || UsuarioFactory.get().idProfile == CONST.PROFILES[1].value) {
        $rootScope.mostrarAdmUsuarios = true;
    }
    $scope.nombreEmpresa = UsuarioFactory.get().nombreEmpresa;
    $rootScope.nombreUsuario = UsuarioFactory.get().nombre;

    // -- Logo de la empresa
    if (UsuarioFactory.get().img != null) {
        $rootScope.setLogoEmpresa('assets/public/' + UsuarioFactory.get().img);
    }

    // -- Agregado de leyenda Plantilla de...
    $scope.plantilla = "";
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.plantilla = "Plantilla de ";
    }

    // -- Vista de parametros segun sea la empresa
    $scope.ocultarParametrosEspecificosDeUnaEmpresa = false;
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.ocultarParametrosEspecificosDeUnaEmpresa = true;
    }

    // -- Vista de parametros segun sea el modo de operacion de la consola
    $scope.modoOperacionConsola = "MULTI";
    /* ParametroService.getModoDeOperacionDeLaConsola().then(function(response){
        $scope.modoOperacionConsola = response.data.data;
    }); */

    // -- Llenado de opciones de parametros
    $scope.opcionesTTS = UtilsService.getOptionsTTS();

    // -- Detalle de los tabs
    $scope.tabs = [
        {
            label: "General",
            contenidoVisible: true
        },
        {
            label: "Guía Interna - Diurno",
            contenidoVisible: false
        },
        {
            label: "Guía Interna - Nocturno",
            contenidoVisible: false
        },
        {
            label: "Guía Externa - Diurno",
            contenidoVisible: true
        },
        {
            label: "Guía Externa - Nocturno",
            contenidoVisible: false
        },
        {
            label: "Horarios de Mensajes de Bienvenida",
            contenidoVisible: false
        },
        {
            label: "Configuración de Fax",
            contenidoVisible: false
        },
        {
            label: "Configuración de Voice Mail",
            contenidoVisible: false
        },
        {
            label: "Modo de Operación",
            contenidoVisible: false
        }
    ]

    // -- Se obtienen los parametros y TTS mode
    $scope.cargando = true;
    $scope.parametros = [];
    function obtenerParametros() {
        $scope.cargando = true;
        EmpresaService.loadEmpresa().then(function(response){
            $scope.tts_mode = response.data.data.tts_mode;

            ParametroService.getParametros().then(function(response){
                angular.forEach(response.data.data, function(value, key) {
                    if (value.par_order != -1) {
                        $scope.parametros[value.par_name] = value;
                    }
                });
    
                // -- Automatic mode
                if ($scope.parametros["AUTOMATIC_MODE"].par_value == "SI") {
                    $scope.automaticMode = "AUTOMATICO";
                } else {
                    $scope.automaticMode = "MANUAL";
                }
    
                // -- Se obtienen los faxes, voice mails y opciones de transferencia (en cadena)
                $scope.faxe = [];
                $scope.vma = [];
                $scope.opcionesDeTransferencia = [];
                FaxService.getFax().then(function(response){
                    $scope.faxes = response.data.data;
    
                    VoiceMailService.getVoiceMail().then(function(response){
                        $scope.vma = response.data.data;
    
                        ParametroService.getOpcionesDeTransferencia().then(function(response){
                            $scope.opcionesDeTransferencia = response.data.data;
                            $scope.cargando = false;
                        }, function() {
                            $scope.cargando = false;
                        });
                    });
                });
            }, function() {
                $scope.cargando = false;
            });
        }, function() {
            $scope.cargando = false;
        });
    }
    obtenerParametros();

    // -- Llenado de opciones de parametros
    $scope.opcionesSiNo = UtilsService.getOptionsYesNo();
    $scope.opcionesOM = UtilsService.getOptionsOperationMode();
    $scope.opcionesAM = UtilsService.getOperationMode();
    $scope.mapQueryValidation = UtilsService.getMapQueryValidation();

    $scope.queryValidation = [];
    for (var i in $scope.mapQueryValidation) {
        var foundItem = $filter('filter')($scope.queryValidation, {query_id: $scope.mapQueryValidation[i].query_id})[0];
        if (foundItem === undefined) {
            $scope.queryValidation.push({ query_id: $scope.mapQueryValidation[i].query_id, data: [] });
        }
    }

    $scope.setTTSMode = function(item) {
        $scope.tts_mode = item;
    }

    $scope.modificarParametros = function() {
        if ($scope.cargando) {
            UtilsService.showToast({delay: 5000, text: "Hay una operación previa en curso, aguarde a que finalice"});
            return;
        }

        if ($scope.parametros['DAY_START_HOUR'].par_value == "") {
            UtilsService.showToast({delay: 5000, text: "Debe completar el horario inicial de mañana (solapa horario de mensajes de bienvenida)"});
            return;
        } else if (!UtilsService.isTime($scope.parametros['DAY_START_HOUR'].par_value)) {
            UtilsService.showToast({delay: 5000, text: "El horario inicial de mañana ingresado es incorrecto, debe ser entre 00:00:00 y 23:59:59"});
            return;
        }
        if ($scope.parametros['NOON_START_HOUR'].par_value == "") {
            UtilsService.showToast({delay: 5000, text: "Debe completar el horario inicial de tarde (solapa horario de mensajes de bienvenida)"});
            return;
        } else if (!UtilsService.isTime($scope.parametros['NOON_START_HOUR'].par_value)) {
            UtilsService.showToast({delay: 5000, text: "El horario inicial de tarde ingresado es incorrecto, debe ser entre 00:00:00 y 23:59:59"});
            return;
        }
        if ($scope.parametros['NIGHT_START_HOUR'].par_value == "") {
            UtilsService.showToast({delay: 5000, text: "Debe completar el horario inicial de noche (solapa horario de mensajes de bienvenida)"});
            return;
        } else if (!UtilsService.isTime($scope.parametros['NIGHT_START_HOUR'].par_value)) {
            UtilsService.showToast({delay: 5000, text: "El horario inicial de noche ingresado es incorrecto, debe ser entre 00:00:00 y 23:59:59"});
            return;
        }
       
        // -- Automatic mode
        if ($scope.automaticMode == "AUTOMATICO") {
            $scope.parametros['AUTOMATIC_MODE'].par_value = "SI";
        } else {
            $scope.parametros['AUTOMATIC_MODE'].par_value = "NO";
        }

        var data = { parametros: [] };
        for (var prop in $scope.parametros) {
            if (!prop.includes("_QV")) {
                data.parametros.push($scope.parametros[prop]);
            }
        }

        $scope.guardando = true;
        var empresa = {
            tts_mode: $scope.tts_mode
        }
        EmpresaService.setTTSMode(empresa).then(function(){
            ParametroService.modificarParametros(data).then(function(response){
                ParametroService.clearDatosTelefonicosPredefinidos();
                
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "Parámetros guardados correctamente"});
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar los parámetros"});
                }
                $scope.guardando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                $scope.guardando = false;
            });
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            $scope.guardando = false;
        });
    };

    $scope.evalSelectModoManual = function(){
        if ($scope.cargando) {
            return false;
        }
        if ($scope.automaticMode == "AUTOMATICO") {
            return true;
        }
        return false;
    }

});