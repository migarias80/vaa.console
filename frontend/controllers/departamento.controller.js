/**
 * DEPARTMENT
 * Controller del listado de departments
 * Posee funciones descontinuadas
 */
app.controller('DepartamentosController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, DepartamentoService, $filter, $route, CONST, $location, ParametroService) {

    // -- Configuracion incial de la pantalla
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Departamentos", false);
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Departamentos");
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

    $scope.selectedTab = 1;
    $scope.cargando = false;

    // -- Obtener datos para grilla
    /*$scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'dep_name',
            direction: 'asc'
        },
        onRegisterApi: function(gridApi) {
            $scope.gridApi = gridApi;
        }
    };*/
    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'dep_name',
            direction: 'asc'
        }
    };
    function obtenerDepartamentos() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        DepartamentoService.getDepartamentoMin().then(function(response){
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            angular.forEach(response.data.data, function(value, key) {
                $scope.gridOptions.data.push(value);
                if (reemplazarItemSeleccionado) {
                    if (value.dep_id == $scope.itemSeleccionado.dep_id) {
                        $scope.viewItem(value, true, true);
                    }
                }
            });
            $scope.cargando = false;

            $scope.states = $scope.loadAll();
        }, function() {
            $scope.cargando = false;
        });
    }
    obtenerDepartamentos();

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { 
            return false; 
        }
        if (item.dep_id == $scope.idSeleccionado) {
            return true;
        }
        return false;
    };

    // -- Context menu
    $scope.menuOptions = [
        {
            text: 'Editar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true, false);
                $scope.editarDepartamento($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true, false);
                $scope.eliminarDepartamento($itemScope.item);
            }
        },
        {
            text: 'Usar como plantilla',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true, false);
                $scope.plantillaDepartamento($itemScope.item);
            }
        },
        {
            displayed: function($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
            }
        }
    ];

    // -- Preview del registro
    $scope.idSeleccionado = undefined;
    $scope.itemSeleccionado = undefined;
    $scope.viewItem = function(item, forcePreview, focusItem) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.dep_id && !forcePreview) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.dep_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                if (focusItem) {
                    // var page = getPageNumber(item.dep_id);
                    // $rootScope.customSelectPage(page);

                    setTimeout(function() {
                        /* var ypos = $('.item-seleccionado').offset().top;
                        $('.mdl-data-table tbody').animate({
                            scrollTop: ypos - 100
                        }, 500); */

                        var indexTr = $("tr").index($('.item-seleccionado'));
                        indexTr --;
                        indexTr *= 41;
                        $('.mdl-data-table tbody').animate({
                            scrollTop: indexTr
                        }, 500);
                    }, 200);
                }
                /* var foundItem = $filter('filter')($scope.gridOptions.data, {dep_id: item.dep_id})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);*/
            }

            if ($scope.itemSeleccionado.dep_daytime_cellular == "" || $scope.itemSeleccionado.dep_daytime_cellular == null) {
                $scope.itemSeleccionado.dep_daytime_cellular = " ";
            }
            if ($scope.itemSeleccionado.dep_nighttime_cellular == "" || $scope.itemSeleccionado.dep_nighttime_cellular == null) {
                $scope.itemSeleccionado.dep_nighttime_cellular = " ";
            }
        }
    };

    /* function getPageNumber(dep_id) {
        var i = 0;
        for (i; i<$scope.gridOptions.grid.filtered.length; i++){
            if ($scope.gridOptions.grid.filtered[i].dep_id == dep_id) {
                break;
            }
        }
        return Math.ceil(i/10);
    }*/

    $scope.hidePreview = function() {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Departamento seleccionado";
        $scope.itemSeleccionado = undefined;
        $scope.searchText = undefined;
        $scope.searchText = "";
    };

    // -- Crear departamento
    $scope.crearDepartamento = function() {
        UtilsService.showDialog("views/departamento.html?_=" + new Date().getTime(), "DepartamentoController").then(function(response) {
            $scope.idSeleccionado = response.id;
            $scope.itemSeleccionado = {dep_id: response.id};
            obtenerDepartamentos();
        }, function() {

        });
    };

    // -- Editar departamento
    $scope.editarDepartamento = function(item) {
        if (!angular.isUndefined(item)) {
            var foundItem = $filter('filter')($scope.gridOptions.data, {dep_id: item.dep_id}, true)[0];
            foundItem.isTemplate = false;
            UtilsService.showDialog("views/departamento.html?_=" + new Date().getTime(), "DepartamentoController", foundItem).then(function(answer) {
                // obtenerDepartamentos();
                // $route.reload();
                refreshRegistro(foundItem.dep_id);
            }, function() {

            });
        }
    };

    // -- Refresh del registro
    function refreshRegistro(id) {
        $scope.cargando_soloProgressBar = true;
        DepartamentoService.loadDepartamento(id).then(function(response){
            if (!angular.isUndefined(response.data)) {
                var length = $scope.gridOptions.data.length;
                for (var i=0; i<length; i++){
                    if ($scope.gridOptions.data[i].dep_id == id) {                    
                        $scope.gridOptions.data[i].dep_daytime_number = response.data.data.dep_daytime_number;
                        $scope.gridOptions.data[i].dep_email = response.data.data.dep_email;
                        $scope.gridOptions.data[i].dep_name = response.data.data.dep_name;
                        $scope.gridOptions.data[i].dep_nighttime_number = response.data.data.dep_nighttime_number;
                        if (response.data.data.dep_daytime_cellular == "" || response.data.data.dep_daytime_cellular == null) {
                            $scope.gridOptions.data[i].dep_daytime_cellular = " ";
                        } else {
                            $scope.gridOptions.data[i].dep_daytime_cellular = response.data.data.dep_daytime_cellular;
                        }
                        if (response.data.data.dep_nighttime_cellular == "" || response.data.data.dep_nighttime_cellular == null) {
                            $scope.gridOptions.data[i].dep_nighttime_cellular = " ";
                        } else {
                            $scope.gridOptions.data[i].dep_nighttime_cellular = response.data.data.dep_nighttime_cellular;
                        }

                        $scope.itemSeleccionado.dep_name = $scope.gridOptions.data[i].dep_name;
                        $scope.itemSeleccionado.dep_daytime_number = $scope.gridOptions.data[i].dep_daytime_number;
                        $scope.itemSeleccionado.dep_nighttime_number = $scope.gridOptions.data[i].dep_nighttime_number;
                        $scope.itemSeleccionado.dep_daytime_cellular = $scope.gridOptions.data[i].dep_daytime_cellular;
                        $scope.itemSeleccionado.dep_nighttime_cellular = $scope.gridOptions.data[i].dep_nighttime_cellular;

                        break;
                    }
                }
            }
            $scope.cargando_soloProgressBar = false;
        });
    }

    // -- Eliminar departamento
    $scope.eliminarDepartamento = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.dep_name + "?").then(function() {
                DepartamentoService.eliminar(item.dep_id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.dep_name + " fue eliminado correctamente"});
                        obtenerDepartamentos();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.dep_name});
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                });

            });
        }
    };

    // -- Crear departamento en base a una plantilla
    $scope.plantillaDepartamento = function(item) {
        if (!angular.isUndefined(item)) {
            var foundItem = $filter('filter')($scope.gridOptions.data, {dep_id: item.dep_id}, true)[0];
            foundItem.isTemplate = true;
            UtilsService.showDialog("views/departamento.html?_=" + new Date().getTime(), "DepartamentoController", foundItem).then(function(response) {
                $scope.idSeleccionado = response.id;
                $scope.itemSeleccionado = {dep_id: response.id};
                obtenerDepartamentos();
            }, function() {

            });
        }
    };

    // -- Muestra las personas del departamento seleccionado
    $scope.verPersonasDelDepartamento = function(item) {
        if (!angular.isUndefined(item)) {
            $location.path('/personas/').search({dep_id: item.dep_id});
        }
    };

    // -- Buqueda dinamica
    $scope.loadAll = function () {
        allStates = [];
        var length = $scope.gridOptions.data.length;
        for (var i=0; i<length; i++){
            allStates.push($scope.gridOptions.data[i]);
            allStates[i].value =
                $scope.gridOptions.data[i].dep_name + " - " +
                $scope.gridOptions.data[i].dep_daytime_number + " - " +
                $scope.gridOptions.data[i].dep_nighttime_number;
            allStates[i].display = $scope.gridOptions.data[i].dep_name;
        }
        return allStates;
    };
    $scope.querySearch = function(query) {
        var result = $filter('filter')($scope.states, {value: query});
        return result;

        // Con promesas
        var results = query ? $scope.states.filter( createFilterFor(query) ) : $scope.states,
            deferred;
        if (self.simulateQuery) {
            deferred = $q.defer();
            $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
            return deferred.promise;
        } else {
            return results;
        }
        return results;
    };
    $scope.searchTextChange = function(text) {

    };
    $scope.selectedItemChange = function(item) {
        $scope.viewItem(item, false, true);
    };

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    // $scope.getPaginationClass = $rootScope.getPaginationClass;

    // -- Obtengo parametros para tenerlos listos al abrir un departamento
    ParametroService.getOpcionesDeTransferencia().then(function(response){ }, function() { });

});


/**
 * DEPARTMENT - DIALOG
 * Controller del dialog del deparment seleccionado
 */
app.controller('DepartamentoController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, DepartamentoService, dialogData, ParametroService, FaxService, VoiceMailService, $rootScope, $filter) {

    $scope.dep_trans_pred_int_diurno = true;
    $scope.dep_trans_pred_int_nocturno = true;
    $scope.dep_trans_pred_ext_diurno = true;
    $scope.dep_trans_pred_ext_nocturno = true;

    // -- Se obtienen opciones de transferencia para cada caso
    /* $scope.optAccion1 = [];
    $scope.optAccion2NoReply = [];
    $scope.optAccion2Busy = [];
    $scope.optAccion3NoReply = [];
    $scope.optAccion3Busy = [];
    ParametroService.getOpcionesDeTransferenciaDepA1().then(function(response){
        angular.copy(response.data.data, $scope.optAccion1);
    }, function() {

    });

    ParametroService.getOpcionesDeTransferenciaDepA2().then(function(response){
        angular.copy(response.data.data, $scope.optAccion2NoReply);
        angular.copy(response.data.data, $scope.optAccion2Busy);
    }, function() {

    });

    ParametroService.getOpcionesDeTransferenciaDepA3().then(function(response){
        angular.copy(response.data.data, $scope.optAccion3NoReply);
        angular.copy(response.data.data, $scope.optAccion3Busy);
    }, function() {

    }); */

    $scope.opcionesDeTransferencia = [];
    ParametroService.getOpcionesDeTransferencia().then(function(response){ 
        angular.copy(response.data.data, $scope.opcionesDeTransferencia);
    }, function() { 

    });

    // -- Se obtienen los faxes y voice mails
    FaxService.getFax().then(function(response){
        $scope.faxes = response.data.data;
        VoiceMailService.getVoiceMail().then(function(response){
            $scope.vma = response.data.data;

            // -- Se obtienen los datos por defecto para cada uno de ellos
            var datosTelefonicosDefault = { };
            ParametroService.getDatosTelefonicosPredefinidos().then(function(response){
                var i = 0;
                for (i in response.data.data) {
                    datosTelefonicosDefault[response.data.data[i].par_name] = response.data.data[i].par_value;
                }
                if (angular.isUndefined($scope.dep_fax_daytime)) {
                    $scope.dep_fax_daytime = datosTelefonicosDefault.FAX_DAY;
                }
                if (angular.isUndefined($scope.dep_fax_nighttime)) {
                    $scope.dep_fax_nighttime = datosTelefonicosDefault.FAX_NIGHT;
                }
                if (angular.isUndefined($scope.dep_vma_daytime)) {
                    $scope.dep_vma_daytime = datosTelefonicosDefault.VOICE_MAIL_DAY;
                }
                if (angular.isUndefined($scope.dep_vma_nighttime)) {
                    $scope.dep_vma_nighttime = datosTelefonicosDefault.VOICE_MAIL_NIGHT;
                }
            }, function() {

            });
        });
    });

    // -- Se obtienen las opciones de confirmacion
    $scope.optConfirmacion = [];
    ParametroService.getOpcionesDeConfirmacion().then(function (response) {
        $scope.optConfirmacion = response.data.data;
    }, function() { });
    $scope.dep_confirmation = 3;

    // -- Salir sin guardar
    $scope.cancelar = function() {
        $mdDialog.cancel();
    };
	
	// -- Aplicado de cambios
	$scope.aplicar = function() {
		$scope.aceptar(true);
	}

    // -- Guardado del departamento
    $scope.aceptar = function(silent) {
        if (UtilsService.isEmpty($scope.dep_name)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el nombre del departamento (solapa datos generales)"});
            return;
        }
        if (UtilsService.isEmpty($scope.dep_daytime_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno diurno (solapa datos telefónicos)"});
            return;
        }
        if (UtilsService.isEmpty($scope.dep_nighttime_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno nocturno (solapa datos telefónicos)"});
            return;
        }
        if (UtilsService.isEmpty($scope.dep_fax_daytime) || $scope.dep_fax_daytime == "") {
            $scope.dep_fax_daytime = null;
            // UtilsService.showToast({delay: 5000, text: "Debe seleccionar el fax diurno (solapa datos telefónicos)"});
            // return;
        }
        if (UtilsService.isEmpty($scope.dep_fax_nighttime) || $scope.dep_fax_nighttime == "") {
            $scope.dep_fax_nighttime = null;
            // UtilsService.showToast({delay: 5000, text: "Debe seleccionar el fax nocturno (solapa datos telefónicos)"});
            // return;
        }
        if (UtilsService.isEmpty($scope.dep_vma_daytime) || $scope.dep_vma_daytime == "") {
            $scope.dep_vma_daytime = null;
            // UtilsService.showToast({delay: 5000, text: "Debe seleccionar el voice mail diurno (solapa datos telefónicos)"});
            // return;
        }
        if (UtilsService.isEmpty($scope.dep_vma_nighttime) || $scope.dep_vma_nighttime == "") {
            $scope.dep_vma_nighttime = null;
            // UtilsService.showToast({delay: 5000, text: "Debe seleccionar el voice mail nocturno (solapa datos telefónicos)"});
            // return;
        }
        if (UtilsService.isEmpty($scope.dep_confirmation)) {
            UtilsService.showToast({delay: 5000, text: "Debe seleccionar la confirmación (solapa reconocimiento de voz)"});
            return;
        }

		if (!UtilsService.isEmpty($scope.dep_fon_name) && $scope.dep_fon_name != "") {
			if (UtilsService.hasNumbers($scope.dep_fon_name)) {
				UtilsService.showToast({delay: 5000, text: "La fonética del departamento no puede contener valores numéricos (solapa reconoc. de voz)"});
				return;
			}
			if (UtilsService.hasUppercase($scope.dep_fon_name)) {
				UtilsService.showToast({delay: 5000, text: "La fonética del departamento no puede contener mayúsculas (solapa reconoc. de voz)"});
				return;
			}
		}

        // -- Dpto
        var data = {
            dep_id: $scope.dep_id,
            dep_name: $scope.dep_name,
            dep_email: $scope.dep_email,
            dep_fon_name: $scope.dep_fon_name,

            dep_daytime_number: $scope.dep_daytime_number,
            dep_nighttime_number: $scope.dep_nighttime_number,
            dep_daytime_cellular: $scope.dep_daytime_cellular,
            dep_nighttime_cellular: $scope.dep_nighttime_cellular,
            dep_int_guide_number: ($scope.dep_int_guide_number == null)? '' : $scope.dep_int_guide_number,
            dep_ext_guide_number: ($scope.dep_ext_guide_number == null)? '' : $scope.dep_ext_guide_number,
            dep_ge_allow_playback_int_number: $scope.dep_ge_allow_playback_int_number,
            dep_gi_allow_playback_int_number: $scope.dep_gi_allow_playback_int_number,

            dep_fax_daytime: $scope.dep_fax_daytime,
            dep_fax_nighttime: $scope.dep_fax_nighttime,
            dep_dialpost_number_fax_daytime: $scope.dep_dialpost_number_fax_daytime,
            dep_dialpost_number_fax_nighttime: $scope.dep_dialpost_number_fax_nighttime,

            dep_vma_daytime: $scope.dep_vma_daytime,
            dep_vma_nighttime: $scope.dep_vma_nighttime,
            dep_dialpost_number_vma_daytime: $scope.dep_dialpost_number_vma_daytime,
            dep_dialpost_number_vma_nighttime: $scope.dep_dialpost_number_vma_nighttime,

            /*dep_ge_allow_playback_int_number: $scope.dep_ge_allow_playback_int_number,
            dep_gi_allow_playback_int_number: $scope.dep_gi_allow_playback_int_number,*/
            dep_allow_htdf_ext: $scope.dep_allow_htdf_ext, // Flag
            dep_allow_htdf_int: $scope.dep_allow_htdf_int, // Flag
            dep_allow_voz_ext: $scope.dep_allow_voz_ext, // Flag
            dep_allow_voz_int: $scope.dep_allow_voz_int, // Flag
            dep_play_msg_info_tranf: $scope.dep_play_msg_info_tranf, // Flag

            dep_confirmation: $scope.dep_confirmation // TODO COMBO VAA_CONFIRMATION_OPTIONS
        };

        // -- Opciones del departamento
        data.dep_transfer_options = [];

        // -- INT DIUR
        if (!$scope.dep_trans_pred_int_diurno) {
            if ((UtilsService.isEmpty($scope.tad_dep_trans_type_int_diur)) ||
                (UtilsService.isEmpty($scope.int_diur_accion1)) ||
                (UtilsService.isEmpty($scope.int_diur_accion2NoReply)) ||
                (UtilsService.isEmpty($scope.int_diur_accion2Busy)) ||
                (UtilsService.isEmpty($scope.int_diur_accion3NoReply)) ||
                (UtilsService.isEmpty($scope.int_diur_accion3Busy))) {
                UtilsService.showToast({delay: 5000, text: "Faltan completar datos en las Acciones para Guía Interna Diurna"});
                return;
            }
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_diur,
                tad_dep_transfer_type: $scope.tad_dep_trans_type_int_diur,
                tad_daytime: true,
                tad_order: 0,
                tad_busy: true,
                tad_tao_id: $scope.int_diur_accion1
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_diur,
                tad_dep_transfer_type: $scope.tad_dep_trans_type_int_diur,
                tad_daytime: true,
                tad_order: 1,
                tad_busy: false,
                tad_tao_id: $scope.int_diur_accion2NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_diur,
                tad_dep_transfer_type: $scope.tad_dep_trans_type_int_diur,
                tad_daytime: true,
                tad_order: 1,
                tad_busy: true,
                tad_tao_id: $scope.int_diur_accion2Busy
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_diur,
                tad_dep_transfer_type: $scope.tad_dep_trans_type_int_diur,
                tad_daytime: true,
                tad_order: 2,
                tad_busy: false,
                tad_tao_id: $scope.int_diur_accion3NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_diur,
                tad_dep_transfer_type: $scope.tad_dep_trans_type_int_diur,
                tad_daytime: true,
                tad_order: 2,
                tad_busy: true,
                tad_tao_id: $scope.int_diur_accion3Busy
            });
        }

        // -- INT NOCT
        if (!$scope.dep_trans_pred_int_nocturno) {
            if ((UtilsService.isEmpty($scope.tad_dep_transfer_type_int_noc)) ||
                (UtilsService.isEmpty($scope.int_noc_accion1)) ||
                (UtilsService.isEmpty($scope.int_noc_accion2NoReply)) ||
                (UtilsService.isEmpty($scope.int_noc_accion2Busy)) ||
                (UtilsService.isEmpty($scope.int_noc_accion3NoReply)) ||
                (UtilsService.isEmpty($scope.int_noc_accion3Busy))) {
                UtilsService.showToast({delay: 5000, text: "Faltan completar datos en las Acciones para Guía Interna Nocturna"});
                return;
            }
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_int_noc,
                tad_daytime: false,
                tad_order: 0,
                tad_busy: true,
                tad_tao_id: $scope.int_noc_accion1
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_int_noc,
                tad_daytime: false,
                tad_order: 1,
                tad_busy: false,
                tad_tao_id: $scope.int_noc_accion2NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_int_noc,
                tad_daytime: false,
                tad_order: 1,
                tad_busy: true,
                tad_tao_id: $scope.int_noc_accion2Busy
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_int_noc,
                tad_daytime: false,
                tad_order: 2,
                tad_busy: false,
                tad_tao_id: $scope.int_noc_accion3NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: true,
                tad_origin_number: $scope.dep_origin_number_int_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_int_noc,
                tad_daytime: false,
                tad_order: 2,
                tad_busy: true,
                tad_tao_id: $scope.int_noc_accion3Busy
            });
        }

        // -- EXT DIUR
        if (!$scope.dep_trans_pred_ext_diurno) {
            if ((UtilsService.isEmpty($scope.tad_dep_transfer_type_ext_diur)) ||
                (UtilsService.isEmpty($scope.ext_diur_accion1)) ||
                (UtilsService.isEmpty($scope.ext_diur_accion2NoReply)) || 
                (UtilsService.isEmpty($scope.ext_diur_accion2Busy)) ||
                (UtilsService.isEmpty($scope.ext_diur_accion3NoReply)) ||
                (UtilsService.isEmpty($scope.ext_diur_accion3Busy))) {
                UtilsService.showToast({delay: 5000, text: "Faltan completar datos en las Acciones para Guía Externa Diurna"});
                return;
            }
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_diur,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_diur,
                tad_daytime: true,
                tad_order: 0,
                tad_busy: true,
                tad_tao_id: $scope.ext_diur_accion1
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_diur,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_diur,
                tad_daytime: true,
                tad_order: 1,
                tad_busy: false,
                tad_tao_id: $scope.ext_diur_accion2NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_diur,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_diur,
                tad_daytime: true,
                tad_order: 1,
                tad_busy: true,
                tad_tao_id: $scope.ext_diur_accion2Busy
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_diur,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_diur,
                tad_daytime: true,
                tad_order: 2,
                tad_busy: false,
                tad_tao_id: $scope.ext_diur_accion3NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_diur,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_diur,
                tad_daytime: true,
                tad_order: 2,
                tad_busy: true,
                tad_tao_id: $scope.ext_diur_accion3Busy
            });
        }

        // -- EXT NOCT
        if (!$scope.dep_trans_pred_ext_nocturno) {
            if ((UtilsService.isEmpty($scope.tad_dep_transfer_type_ext_noc)) ||
                (UtilsService.isEmpty($scope.ext_noc_accion1)) ||
                (UtilsService.isEmpty($scope.ext_noc_accion2NoReply)) ||
                (UtilsService.isEmpty($scope.ext_noc_accion2Busy)) ||
                (UtilsService.isEmpty($scope.ext_noc_accion3NoReply)) ||
                (UtilsService.isEmpty($scope.ext_noc_accion3Busy))) {
                UtilsService.showToast({delay: 5000, text: "Faltan completar datos en las Acciones para Guía Externa Nocturna"});
                return;
            }
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_noc,
                tad_daytime: false,
                tad_order: 0,
                tad_busy: true,
                tad_tao_id: $scope.ext_noc_accion1
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_noc,
                tad_daytime: false,
                tad_order: 1,
                tad_busy: false,
                tad_tao_id: $scope.ext_noc_accion2NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_noc,
                tad_daytime: false,
                tad_order: 1,
                tad_busy: true,
                tad_tao_id: $scope.ext_noc_accion2Busy
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_noc,
                tad_daytime: false,
                tad_order: 2,
                tad_busy: false,
                tad_tao_id: $scope.ext_noc_accion3NoReply
            });
            data.dep_transfer_options.push({
                tad_int_guide: false,
                tad_origin_number: $scope.dep_origin_number_ext_noc,
                tad_dep_transfer_type: $scope.tad_dep_transfer_type_ext_noc,
                tad_daytime: false,
                tad_order: 2,
                tad_busy: true,
                tad_tao_id: $scope.ext_noc_accion3Busy
            });
        }
        DepartamentoService.guardar(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Departamento guardado correctamente"});
				if (silent) {
					return;
				}
				if (response.data.data !== undefined && response.data.data.id !== undefined) {
                    $mdDialog.hide({message: "OK", id: response.data.data.id});
                } else {
                    $mdDialog.hide({message: "OK", id: $scope.dep_id});
                }
            } else {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(response)});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    };

    $scope.isEMailValid = function(value) {
        return UtilsService.isEMailValid(value);
    };

    // -- Redefinir en $scope la funcion del $rootScope para la barra de progreso
    $scope.getProgressBarClass = $rootScope.getProgressBarClass;

    // -- Redefinir en $scope la funcion del $rootScope para el checkbox
    $scope.getCheckboxClass = $rootScope.getCheckboxClass;

    // -- Valores por defecto on Transferncia predeterminada Change (Posterior al click, el valor ya se encuentra cambiado)
    $scope.parametrosIntDiur = [];
    $scope.parametrosIntNoct = [];
    $scope.parametrosExtDiur = [];
    $scope.parametrosExtNoct = [];
    $scope.transferenciaPredeterminadaChange = function (listaName) {
        if  ((listaName == 'accionesIntDiur' && $scope.parametrosIntDiur.length == 0) 
            || (listaName == 'accionesIntNoct' && $scope.parametrosIntNoct.length == 0)
            || (listaName == 'accionesExtDiur' && $scope.parametrosExtDiur.length == 0)
            || (listaName == 'accionesExtNoct' && $scope.parametrosExtNoct.length == 0)) {
            $scope.cargando = true;
            var parametrosFilter = {};
            if (listaName == 'accionesIntDiur') {
                parametrosFilter = {
                    parametros: 
                        ['GI_ACTION_1_DAY', 'GI_ACTION_2_NO_ANSWER_DAY', 'GI_ACTION_3_NO_ANSWER_DAY', 'GI_ACTION_2_BUSY_DAY', 'GI_ACTION_3_BUSY_DAY',
                        'GI_SEC_ACTION_1_DAY', 'GI_SEC_ACTION_2_NO_ANSWER_DAY', 'GI_SEC_ACTION_3_NO_ANSWER_DAY', 'GI_SEC_ACTION_2_BUSY_DAY', 'GI_SEC_ACTION_3_BUSY_DAY',
                        'GI_CEL_ACTION_1_DAY', 'GI_CEL_ACTION_2_NO_ANSWER_DAY', 'GI_CEL_ACTION_3_NO_ANSWER_DAY', 'GI_CEL_ACTION_2_BUSY_DAY', 'GI_CEL_ACTION_3_BUSY_DAY']
                }
            } else if (listaName == 'accionesIntNoct') {
                parametrosFilter = {
                    parametros: [
                        'GI_ACTION_1_NIGHT', 'GI_ACTION_2_NO_ANSWER_NIGHT', 'GI_ACTION_3_NO_ANSWER_NIGHT', 'GI_ACTION_2_BUSY_NIGHT', 'GI_ACTION_3_BUSY_NIGHT',
                        'GI_SEC_ACTION_1_NIGHT', 'GI_SEC_ACTION_2_NO_ASW_NIGHT', 'GI_SEC_ACTION_3_NO_ASW_NIGHT', 'GI_SEC_ACTION_2_BUSY_NIGHT', 'GI_SEC_ACTION_3_BUSY_NIGHT',
                        'GI_CEL_ACTION_1_NIGHT', 'GI_CEL_ACTION_2_NO_ASW_NIGHT', 'GI_CEL_ACTION_3_NO_ASW_NIGHT', 'GI_CEL_ACTION_2_BUSY_NIGHT', 'GI_CEL_ACTION_3_BUSY_NIGHT']
                }
            } else if (listaName == 'accionesExtDiur') {
                parametrosFilter = {
                    parametros: [
                        'ACTION_1_DAY', 'ACTION_2_NO_ANSWER_DAY', 'ACTION_3_NO_ANSWER_DAY', 'ACTION_2_BUSY_DAY', 'ACTION_3_BUSY_DAY',
                        'SEC_ACTION_1_DAY', 'SEC_ACTION_2_NO_ANSWER_DAY', 'SEC_ACTION_3_NO_ANSWER_DAY', 'SEC_ACTION_2_BUSY_DAY', 'SEC_ACTION_3_BUSY_DAY',
                        'CEL_ACTION_1_DAY', 'CEL_ACTION_2_NO_ANSWER_DAY', 'CEL_ACTION_3_NO_ANSWER_DAY', 'CEL_ACTION_2_BUSY_DAY', 'CEL_ACTION_3_BUSY_DAY']
                }
            } else if (listaName == 'accionesExtNoct') {
                parametrosFilter = {
                    parametros: [
                        'ACTION_1_NIGHT', 'ACTION_2_NO_ANSWER_NIGHT', 'ACTION_3_NO_ANSWER_NIGHT', 'ACTION_2_BUSY_NIGHT', 'ACTION_3_BUSY_NIGHT',
                        'SEC_ACTION_1_NIGHT', 'SEC_ACTION_2_NO_ANSWER_NIGHT', 'SEC_ACTION_3_NO_ANSWER_NIGHT', 'SEC_ACTION_2_BUSY_NIGHT', 'SEC_ACTION_3_BUSY_NIGHT',
                        'CEL_ACTION_1_NIGHT', 'CEL_ACTION_2_NO_ANSWER_NIGHT', 'CEL_ACTION_3_NO_ANSWER_NIGHT', 'CEL_ACTION_2_BUSY_NIGHT', 'CEL_ACTION_3_BUSY_NIGHT']
                }
            }
            ParametroService.getParametros(parametrosFilter).then(function(response){
                var length = response.data.data.length;
                if (listaName == 'accionesIntDiur') {
                    for (var i=0; i<length; i++) {
                        $scope.parametrosIntDiur.push(response.data.data[i]);
                    }
                } else if (listaName == 'accionesIntNoct') {
                    for (var i=0; i<length; i++) {
                        $scope.parametrosIntNoct.push(response.data.data[i]);
                    }
                } else if (listaName == 'accionesExtDiur') {
                    for (var i=0; i<length; i++) {
                        $scope.parametrosExtDiur.push(response.data.data[i]);
                    }
                } else if (listaName == 'accionesExtNoct') {
                    for (var i=0; i<length; i++) {
                        $scope.parametrosExtNoct.push(response.data.data[i]);
                    }
                }
                $scope.setTransferenciaPredeterminada(listaName, 2);
                $scope.cargando = false;
            });
        } else {
            $scope.setTransferenciaPredeterminada(listaName, 2);
        }
    }

    // -- Valores por defecto on Destino de transferencia Change
    $scope.destinoTransferenciaChange = function (listaName, destinoTransferencia) {
        $scope.setTransferenciaPredeterminada (listaName, destinoTransferencia);
    }

    // -- Se setean los valores predeterminados de las opciones de transferencia
    $scope.setTransferenciaPredeterminada = function (listaName, trans_type) {
        if (listaName == 'accionesIntDiur') {
            $scope.tad_dep_trans_type_int_diur = trans_type;
            if (trans_type == 1) {
                $scope.int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_1_DAY'})[0].par_value;
                $scope.int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_3_BUSY_DAY'})[0].par_value;
            } else if (trans_type == 2) {
                $scope.int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_1_DAY'})[0].par_value;
                $scope.int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_3_BUSY_DAY'})[0].par_value;
            } else if (trans_type == 3) {
                $scope.int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_1_DAY'})[0].par_value;
                $scope.int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_3_BUSY_DAY'})[0].par_value;
            }
        } else if (listaName == 'accionesIntNoct') {
            $scope.tad_dep_transfer_type_int_noc = trans_type;
            if (trans_type == 1) {
                $scope.int_noc_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_1_NIGHT'})[0].par_value;
                $scope.int_noc_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_2_NO_ASW_NIGHT'})[0].par_value;
                $scope.int_noc_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.int_noc_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_3_NO_ASW_NIGHT'})[0].par_value;
                $scope.int_noc_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_3_BUSY_NIGHT'})[0].par_value;
            } else if (trans_type == 2) {
                $scope.int_noc_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_1_NIGHT'})[0].par_value;
                $scope.int_noc_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.int_noc_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.int_noc_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.int_noc_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_3_BUSY_NIGHT'})[0].par_value;
            } else if (trans_type == 3) {
                $scope.int_noc_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_1_NIGHT'})[0].par_value;
                $scope.int_noc_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.int_noc_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.int_noc_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.int_noc_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_3_BUSY_NIGHT'})[0].par_value;
            }
        } else if (listaName == 'accionesExtDiur') {
            $scope.tad_dep_transfer_type_ext_diur = trans_type;
            if (trans_type == 1) {
                $scope.ext_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_1_DAY'})[0].par_value;
                $scope.ext_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.ext_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_3_BUSY_DAY'})[0].par_value;
            } else if (trans_type == 2) {
                $scope.ext_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_1_DAY'})[0].par_value;
                $scope.ext_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.ext_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_3_BUSY_DAY'})[0].par_value;
            } else if (trans_type == 3) {
                $scope.ext_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_1_DAY'})[0].par_value;
                $scope.ext_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_2_BUSY_DAY'})[0].par_value;
                $scope.ext_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                $scope.ext_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_3_BUSY_DAY'})[0].par_value;
            }
        } else if (listaName == 'accionesExtNoct') {
            $scope.tad_dep_transfer_type_ext_noc = trans_type;
            if (trans_type == 1) {
                $scope.ext_noc_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_1_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_3_BUSY_NIGHT'})[0].par_value;
            } else if (trans_type == 2) {
                $scope.ext_noc_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_1_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_3_BUSY_NIGHT'})[0].par_value;
            } else if (trans_type == 3) {
                $scope.ext_noc_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_1_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_2_BUSY_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                $scope.ext_noc_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_3_BUSY_NIGHT'})[0].par_value;
            }
        }
    };

    // -- Valores por defecto de los DTMF
    $scope.actualizarDTMFDiurno = function() {
		if ($scope.dep_int_guide_number == null || $scope.dep_int_guide_number == "") {
			$scope.dep_int_guide_number = $scope.dep_daytime_number;
			$scope.dep_ext_guide_number = $scope.dep_daytime_number;
		}
    }
    $scope.actualizarDTMFNocturno = function() {
		if ($scope.dep_ext_guide_number == null || $scope.dep_ext_guide_number == "") {
			$scope.dep_ext_guide_number = $scope.dep_daytime_number;
		}
    }

    // -- Carga del Departamento
    $scope.cargando = false;
    if (!angular.isUndefined(dialogData)) {

        // Obtener datos
        $scope.cargando = true;
        DepartamentoService.loadDepartamento(dialogData.dep_id).then(function(response){
            if (!angular.isUndefined(response.data)) {
                // Pasar datos obtenidos a $scope
                for (var name in response.data.data) {
                    $scope[name] = response.data.data[name];
                }
                $scope.titulo = "Departamento " + $scope.dep_name;

                // Data de plantilla
                if (dialogData.isTemplate) {
                    delete $scope.dep_id;
                    delete $scope.dep_name;
                    delete $scope.dep_email;
                    delete $scope.dep_daytime_number;
                    delete $scope.dep_daytime_cellular;
                    delete $scope.dep_nighttime_number;
                    delete $scope.dep_nighttime_cellular;
                    $scope.titulo = "Nuevo departamento";
                }

                // Armado de transfer options
                if (!angular.isUndefined(response.data.data.transfer_options) && response.data.data.transfer_options != null) {
                    var transferOptionsLength = response.data.data.transfer_options.length;
                    for (var i=0; i<transferOptionsLength; i++) {
                        var transfer_options = response.data.data.transfer_options[i];
                        switch(UtilsService.evalAccion(transfer_options)) {
                            case "INT-DIUR":
                                $scope.dep_trans_pred_int_diurno = false;
                                $scope.tad_dep_trans_type_int_diur = UtilsService.getDestinoTransferencia(transfer_options.tad_dep_transfer_type);
                                $scope.dep_origin_number_int_diur = transfer_options.tad_origin_number == 'DEFAULT'? '' : transfer_options.tad_origin_number;
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        $scope.int_diur_accion1 = transfer_options.tad_tao_id;
                                        break;
                                    case "A2":
                                        $scope.int_diur_accion2NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A2B":
                                        $scope.int_diur_accion2Busy = transfer_options.tad_tao_id;
                                        break;
                                    case "A3":
                                        $scope.int_diur_accion3NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A3B":
                                        $scope.int_diur_accion3Busy = transfer_options.tad_tao_id;
                                        break;
                                }
                                break;
                            case "INT-NOCT":
                                $scope.dep_trans_pred_int_nocturno = false;
                                $scope.tad_dep_transfer_type_int_noc = UtilsService.getDestinoTransferencia(transfer_options.tad_dep_transfer_type);
                                $scope.dep_origin_number_int_noc = transfer_options.tad_origin_number == 'DEFAULT'? '' : transfer_options.tad_origin_number;
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        $scope.int_noc_accion1 = transfer_options.tad_tao_id;
                                        break;
                                    case "A2":
                                        $scope.int_noc_accion2NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A2B":
                                        $scope.int_noc_accion2Busy = transfer_options.tad_tao_id;
                                        break;
                                    case "A3":
                                        $scope.int_noc_accion3NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A3B":
                                        $scope.int_noc_accion3Busy = transfer_options.tad_tao_id;
                                        break;
                                }
                                break;
                            case "EXT-DIUR":
                                $scope.dep_trans_pred_ext_diurno = false;
                                $scope.tad_dep_transfer_type_ext_diur = UtilsService.getDestinoTransferencia(transfer_options.tad_dep_transfer_type);
                                $scope.dep_origin_number_ext_diur = transfer_options.tad_origin_number == 'DEFAULT'? '' : transfer_options.tad_origin_number;
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        $scope.ext_diur_accion1 = transfer_options.tad_tao_id;
                                        break;
                                    case "A2":
                                        $scope.ext_diur_accion2NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A2B":
                                        $scope.ext_diur_accion2Busy = transfer_options.tad_tao_id;
                                        break;
                                    case "A3":
                                        $scope.ext_diur_accion3NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A3B":
                                        $scope.ext_diur_accion3Busy = transfer_options.tad_tao_id;
                                        break;
                                }
                                break;
                            case "EXT-NOCT":
                                $scope.dep_trans_pred_ext_nocturno = false;
                                $scope.tad_dep_transfer_type_ext_noc = UtilsService.getDestinoTransferencia(transfer_options.tad_dep_transfer_type);
                                $scope.dep_origin_number_ext_noc = transfer_options.tad_origin_number == 'DEFAULT'? '' : transfer_options.tad_origin_number;
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        $scope.ext_noc_accion1 = transfer_options.tad_tao_id;
                                        break;
                                    case "A2":
                                        $scope.ext_noc_accion2NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A2B":
                                        $scope.ext_noc_accion2Busy = transfer_options.tad_tao_id;
                                        break;
                                    case "A3":
                                        $scope.ext_noc_accion3NoReply = transfer_options.tad_tao_id;
                                        break;
                                    case "A3B":
                                        $scope.ext_noc_accion3Busy = transfer_options.tad_tao_id;
                                        break;
                                }
                                break;
                        }
                    }
                }
                $scope.cargando = false;
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cargar el departamento"});
                return;
            }
        }, function() {
            UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cargar el departamento"});
            return;
        });
    } else {
        $scope.titulo = "Nuevo departamento";

        // -- Carga de valores por defecto (no combos)
        $scope.dep_gi_allow_playback_int_number = false;
        $scope.dep_ge_allow_playback_int_number = false;
        $scope.dep_allow_htdf_int = true;
        $scope.dep_allow_htdf_ext = true;
        $scope.dep_allow_voz_int = true;
        $scope.dep_allow_voz_ext = true;
    }

    // -- FIX access to root scope from modal
    $scope.getEnvironmentClass = function() {
        return $rootScope.getEnvironmentClass();
    }

});