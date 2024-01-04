/**
 * PHONE BOOK
 * Controller del listado de phone books
 */
app.controller('PersonasController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, PersonaService, DepartamentoService, $filter, $route, CONST, $location, ParametroService) {

    // -- Configuracion incial de la pantalla
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Personas", false);
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Personas");
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
    $scope.cargando_soloProgressBar = false;

    // -- Obtener datos para grilla
    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'phb_full_name',
            direction: 'asc'
        }
    };
    function obtenerPersonas() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        var filter = {};
        if (typeof $location.search().dep_id != 'undefined') {
            filter.dep_id = $location.search().dep_id;
            DepartamentoService.loadDepartamento(filter.dep_id).then(function(response){
                if (!angular.isUndefined(response.data)) {
                    $scope.searchText = response.data.data.dep_name;
                }
            });
        }
        PersonaService.getPersonaMinNoCast(filter).then(function(response){
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            $scope.gridOptions.data = response.data.data;
            if (reemplazarItemSeleccionado) {
                for (var i in response.data.data) {  
                    if (response.data.data[i].phb_id == $scope.itemSeleccionado.phb_id) {
                        $scope.viewItem(response.data.data[i], true, true);
                        break;
                    }
                }
            }
            $scope.cargando = false;

            $scope.states = $scope.loadAll();
        }, function() {
            $scope.cargando = false;
        });
    }
    obtenerPersonas();

    $scope.getNombreItemSeleccionado = function() {
        if (angular.isUndefined($scope.itemSeleccionado)) {
            return "Persona seleccionado";
        } else {
            return $scope.itemSeleccionado.phb_id;
        }
    };

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.phb_id == $scope.idSeleccionado) {
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
                $scope.editarPersona($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true, false);
                $scope.eliminarPersona($itemScope.item);
            }
        },
        {
            text: 'Usar como plantilla',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true, false);
                $scope.plantillaPersona($itemScope.item);
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
            if ($scope.idSeleccionado == item.phb_id && !forcePreview) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.phb_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                if (focusItem) {
                    setTimeout(function() {
                        var indexTr = $("tr").index($('.item-seleccionado'));
                        indexTr --;
                        indexTr *= document.getElementsByClassName('tr-datos')[0].offsetHeight;                        ;
                        $('.mdl-data-table tbody').animate({
                            scrollTop: indexTr
                        }, 500);
                    }, 200);
                }
            }

            if ($scope.itemSeleccionado.sec_name == "" || $scope.itemSeleccionado.sec_name == null) {
                $scope.itemSeleccionado.sec_name = " ";
            }
            if ($scope.itemSeleccionado.phb_daytime_cellular == "" || $scope.itemSeleccionado.phb_daytime_cellular == null) {
                $scope.itemSeleccionado.phb_daytime_cellular = " ";
            }
            if ($scope.itemSeleccionado.phb_nighttime_cellular == "" || $scope.itemSeleccionado.phb_nighttime_cellular == null) {
                $scope.itemSeleccionado.phb_nighttime_cellular = " ";
            }
        }
    };

    $scope.hidePreview = function() {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Persona seleccionada";
        $scope.itemSeleccionado = undefined;
        $scope.searchText = undefined;
        $scope.searchText = "";
    };

    // -- Crear persona
    $scope.crearPersona = function() {
        UtilsService.showDialog("views/persona.html?_=" + new Date().getTime(), "PersonaController").then(function(response) {
            $scope.idSeleccionado = response.id;
            $scope.itemSeleccionado = {phb_id: response.id};
            obtenerPersonas();
        }, function() {

        });
    };

    // -- Editar persona
    $scope.editarPersona = function(item) {
        if (!angular.isUndefined(item)) {
            var foundItem = $filter('filter')($scope.gridOptions.data, {phb_id: item.phb_id})[0];
            foundItem.isTemplate = false;
            UtilsService.showDialog("views/persona.html?_=" + new Date().getTime(), "PersonaController", foundItem).then(function(answer) {
                refreshRegistro(foundItem.phb_id);
            }, function() {

            });
        }
    };

    // -- Refresh del registro
    function refreshRegistro(id) {
        $scope.cargando_soloProgressBar = true;
        PersonaService.loadPersona(id).then(function(response){
            if (!angular.isUndefined(response.data)) {
                var length = $scope.gridOptions.data.length;
                for (var i=0; i<length; i++){
                    if ($scope.gridOptions.data[i].phb_id == id) {
                        $scope.gridOptions.data[i].dep_name = response.data.data.dep_name;
                        $scope.gridOptions.data[i].phb_daytime_number = response.data.data.phb_daytime_number;
                        $scope.gridOptions.data[i].phb_full_name = response.data.data.phb_full_name;
                        $scope.gridOptions.data[i].phb_id = response.data.data.phb_id;
                        $scope.gridOptions.data[i].phb_nighttime_number = response.data.data.phb_nighttime_number;
                        if (response.data.data.sec_name == "" || response.data.data.sec_name == null) {
                            $scope.gridOptions.data[i].sec_name = " ";
                        } else {
                            $scope.gridOptions.data[i].sec_name = response.data.data.sec_name;
                        }
                        if (response.data.data.phb_daytime_cellular == "" || response.data.data.phb_daytime_cellular == null) {
                            $scope.gridOptions.data[i].phb_daytime_cellular = " ";
                        } else {
                            $scope.gridOptions.data[i].phb_daytime_cellular = response.data.data.phb_daytime_cellular;
                        }
                        if (response.data.data.phb_nighttime_cellular == "" || response.data.data.phb_nighttime_cellular == null) {
                            $scope.gridOptions.data[i].phb_nighttime_cellular = " ";
                        } else {
                            $scope.gridOptions.data[i].phb_nighttime_cellular = response.data.data.phb_nighttime_cellular;
                        }

                        $scope.itemSeleccionado.phb_full_name = $scope.gridOptions.data[i].phb_full_name;
                        $scope.itemSeleccionado.dep_name = $scope.gridOptions.data[i].dep_name;
                        $scope.itemSeleccionado.sec_name = $scope.gridOptions.data[i].sec_name;
                        $scope.itemSeleccionado.phb_daytime_number = $scope.gridOptions.data[i].phb_daytime_number;
                        $scope.itemSeleccionado.phb_nighttime_number = $scope.gridOptions.data[i].phb_nighttime_number;
                        $scope.itemSeleccionado.phb_daytime_cellular = $scope.gridOptions.data[i].phb_daytime_cellular;
                        $scope.itemSeleccionado.phb_nighttime_cellular = $scope.gridOptions.data[i].phb_nighttime_cellular;

                        break;
                    }
                }
            }
            $scope.cargando_soloProgressBar = false;
        });
    }

    // -- Eliminar persona
    $scope.eliminarPersona = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.phb_full_name + "?").then(function() {
                PersonaService.eliminar(item.phb_id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.phb_full_name + " fue eliminado correctamente"});
                        obtenerPersonas();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.phb_full_name});
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                });

            });
        }
    };

    // -- Crear persona en base a una plantilla
    $scope.plantillaPersona = function(item) {
        if (!angular.isUndefined(item)) {
            var foundItem = $filter('filter')($scope.gridOptions.data, {phb_id: item.phb_id})[0];
            foundItem.isTemplate = true;
            UtilsService.showDialog("views/persona.html", "PersonaController", foundItem).then(function(response) {
                $scope.idSeleccionado = response.id;
                $scope.itemSeleccionado = {phb_id: response.id};
                obtenerPersonas();
            }, function() {

            });
        }
    };

    // -- Buqueda dinamica
    $scope.loadAll = function () {
        allStates = [];
        if ($scope.gridOptions.data != undefined) {
            var length = $scope.gridOptions.data.length;
            for (var i=0; i<length; i++){
                allStates.push($scope.gridOptions.data[i]);
                allStates[i].value =
                    $scope.gridOptions.data[i].phb_full_name + " - " +
                    $scope.gridOptions.data[i].dep_name + " - " +
                    $scope.gridOptions.data[i].phb_daytime_number + " - " +
                    $scope.gridOptions.data[i].phb_ext_guide_number + " - ";
                allStates[i].display = $scope.gridOptions.data[i].phb_full_name + " (" + $scope.gridOptions.data[i].dep_name + ")";
            }
        }
        return allStates;
    };
    $scope.querySearch = function(query) {
        var result = $filter('filter')($scope.states, {value: query});
        return result;
    };
    $scope.searchTextChange = function(text) {

    };
    $scope.selectedItemChange = function(item) {
        $scope.viewItem(item, false, true);
    };

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    // $scope.getPaginationClass = $rootScope.getPaginationClass;

    // -- Obtengo parametros para tenerlos listos al abrir una persona
    ParametroService.getOpcionesDeTransferencia().then(function(response){ }, function() { });

});


/**
 * PHONE BOOK - DIALOG
 * Controller del dialog del phone book seleccionado
 */
app.controller('PersonaController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, DepartamentoService, dialogData, ParametroService, FaxService, VoiceMailService, PersonaService, $rootScope, $filter) {

    $scope.phb_trans_pred_int_diurno = true;
    $scope.phb_trans_pred_int_nocturno = true;
    $scope.phb_trans_pred_ext_diurno = true;
    $scope.phb_trans_pred_ext_nocturno = true;
    $scope.tabGuiaInterna = { contenidoVisible: false };
    $scope.tabGuiaExterna = { contenidoVisible: false }
    $scope.tabOpcionesAvanzadas = { contenidoVisible: false }
    
    $scope.opcionesDeTransferencia = [];
    ParametroService.getOpcionesDeTransferencia().then(function(response){ 
        angular.copy(response.data.data, $scope.opcionesDeTransferencia);
    }, function() { 

    });

    // -- Se obtienen las opciones de confirmacion
    $scope.optConfirmacion = [];
    ParametroService.getOpcionesDeConfirmacion().then(function (response) {
        angular.copy(response.data.data, $scope.optConfirmacion);
    }, function() { });
    $scope.phb_confirmation = 3;

    // -- Se obtienen las opciones de gramatica
    $scope.optGrammar = [];
    ParametroService.getOpcionesDeGramatica().then(function (response) {
        var optGrammar = [];
        var i = 0;
        angular.forEach(response.data.data, function(value, key) {
            var optGrammarValue = {
                oig_description: value.oig_description,
                oig_id: value.oig_id,
                oig_index: key
            };
            optGrammar.push(optGrammarValue);
        });
        $scope.optGrammar = optGrammar;
    }, function() {

    });

    // -- Se obtienen los faxes y voice mails y sus valores por defecto
    FaxService.getFax().then(function(response){
        $scope.faxes = response.data.data;
        VoiceMailService.getVoiceMail().then(function(response){
            $scope.vma = response.data.data;

            var datosTelefonicosDefault = { };
            if (angular.isUndefined(dialogData)) {
                ParametroService.getDatosTelefonicosPredefinidos().then(function(response){
                    $scope.phb_fax_daytime = $filter('filter')(response.data.data, {par_name: 'FAX_DAY'})[0].par_value;
                    $scope.phb_fax_nighttime = $filter('filter')(response.data.data, {par_name: 'FAX_NIGHT'})[0].par_value;
                    $scope.phb_vma_daytime = $filter('filter')(response.data.data, {par_name: 'VOICE_MAIL_DAY'})[0].par_value;
                    $scope.phb_vma_nighttime = $filter('filter')(response.data.data, {par_name: 'VOICE_MAIL_NIGHT'})[0].par_value;
                }, function() {

                });
            }
        });
    });

    // -- Se obtienen los departamentos
    $scope.departamentos = [];
    DepartamentoService.getDepartamento().then(function (response) {
        angular.forEach(response.data.data, function(value, key) {
            $scope.departamentos.push(value);
        });
    });

    // -- Se obtienen los asistentes y personas transferibles
    $scope.secretarias = [];
    $scope.transferibles = [];
    PersonaService.getSecretariasYTrasnferibles().then(function (response) {
        angular.forEach(response.data.data.secretarias, function(value, key) {
            $scope.secretarias.push(value);
        });
        angular.forEach(response.data.data.transferibles, function(value, key) {
            $scope.transferibles.push(value);
        });
    });

    $scope.cancelar = function() {
        $mdDialog.cancel();
    };

    // -- Acciones INT DIUR
    $scope.accionesIntDiur = [];
    var data = {
        phb_trans_pred_int_diurno: true,
        phb_origin_number_int_diur: "",
        tap_phb_trans_type_int_diur: null,
        int_diur_accion1: null,
        int_diur_accion2NoReply: null,
        int_diur_accion2Busy: null,
        int_diur_accion3NoReply: null,
        int_diur_accion3Busy: null,
        int_diur_accion1_transfPhbId: null,
        int_diur_accion2NoReply_transfPhbId: null,
        int_diur_accion2Busy_transfPhbId: null,
        int_diur_accion3NoReply_transfPhbId: null,
        int_diur_accion3Busy_transfPhbId: null
    }
    $scope.accionesIntDiur.push(data);

    // -- Acciones INT NOCT
    $scope.accionesIntNoct = [];
    var data = {
        phb_trans_pred_int_diurno: true,
        phb_origin_number_int_diur: "",
        tap_phb_trans_type_int_diur: null,
        int_diur_accion1: null,
        int_diur_accion2NoReply: null,
        int_diur_accion2Busy: null,
        int_diur_accion3NoReply: null,
        int_diur_accion3Busy: null,
        int_diur_accion1_transfPhbId: null,
        int_diur_accion2NoReply_transfPhbId: null,
        int_diur_accion2Busy_transfPhbId: null,
        int_diur_accion3NoReply_transfPhbId: null,
        int_diur_accion3Busy_transfPhbId: null
    };
    $scope.accionesIntNoct.push(data);

    // -- Acciones EXT DIUR
    $scope.accionesExtDiur = [];
    var data = {
        phb_trans_pred_int_diurno: true,
        phb_origin_number_int_diur: "",
        tap_phb_trans_type_int_diur: null,
        int_diur_accion1: null,
        int_diur_accion2NoReply: null,
        int_diur_accion2Busy: null,
        int_diur_accion3NoReply: null,
        int_diur_accion3Busy: null,
        int_diur_accion1_transfPhbId: null,
        int_diur_accion2NoReply_transfPhbId: null,
        int_diur_accion2Busy_transfPhbId: null,
        int_diur_accion3NoReply_transfPhbId: null,
        int_diur_accion3Busy_transfPhbId: null
    };
    $scope.accionesExtDiur.push(data);

    // -- Acciones EXT NOCT
    $scope.accionesExtNoct = [];
    var data = {
        phb_trans_pred_int_diurno: true,
        phb_origin_number_int_diur: "",
        tap_phb_trans_type_int_diur: null,
        int_diur_accion1: null,
        int_diur_accion2NoReply: null,
        int_diur_accion2Busy: null,
        int_diur_accion3NoReply: null,
        int_diur_accion3Busy: null,
        int_diur_accion1_transfPhbId: null,
        int_diur_accion2NoReply_transfPhbId: null,
        int_diur_accion2Busy_transfPhbId: null,
        int_diur_accion3NoReply_transfPhbId: null,
        int_diur_accion3Busy_transfPhbId: null
    };
    $scope.accionesExtNoct.push(data);

    // -- Valores por defecto on Transferncia predeterminada Change (Posterior al click, el valor ya se encuentra cambiado)
    // -- Primero se obtienen los valores por defecto segun la lista seleccionada
    $scope.parametrosIntDiur = [];
    $scope.parametrosIntNoct = [];
    $scope.parametrosExtDiur = [];
    $scope.parametrosExtNoct = [];
    $scope.transferenciaPredeterminadaChange = function (lista, listaName, callbackDoAgregarAccion) {
        var valor = lista[0].phb_trans_pred_int_diurno;
        if (valor) { return; }

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
                if (callbackDoAgregarAccion !== undefined) {
                    callbackDoAgregarAccion(lista, listaName);
                } else {
                    $scope.setTransferenciaPredeterminada (lista, listaName, 2);
                }
                $scope.cargando = false;
            });
        } else {
            if (callbackDoAgregarAccion !== undefined) {
                callbackDoAgregarAccion(lista, listaName);
            } else {
                $scope.setTransferenciaPredeterminada (lista, listaName, 2);
            }
        }
    };

    // -- Valores por defecto on Destino de transferencia Change
    $scope.destinoTransferenciaChange = function (lista, listaName, destinoTransferencia, accionActual) {
        $scope.setTransferenciaPredeterminada ([lista], listaName, destinoTransferencia);
    }

    // -- Se setean los valores predeterminados de las opciones de transferencia
    $scope.setTransferenciaPredeterminada = function (lista, listaName, trans_type) {
        var valor = lista[0].phb_trans_pred_int_diurno;
        var length = lista.length;
        for (var i=0; i<length; i++) {
            lista[i].phb_trans_pred_int_diurno = valor; // null;
            lista[i].phb_origin_number_int_diur = "";
            lista[i].tap_phb_trans_type_int_diur = trans_type;
            if (listaName == 'accionesIntDiur') {
                if (trans_type == 1) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_CEL_ACTION_3_BUSY_DAY'})[0].par_value;
                } else if (trans_type == 2) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_ACTION_3_BUSY_DAY'})[0].par_value;
                } else if (trans_type == 3) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntDiur, {par_name: 'GI_SEC_ACTION_3_BUSY_DAY'})[0].par_value;
                }
            } else if (listaName == 'accionesIntNoct') {
                if (trans_type == 1) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_2_NO_ASW_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_3_NO_ASW_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_CEL_ACTION_3_BUSY_NIGHT'})[0].par_value;
                } else if (trans_type == 2) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_ACTION_3_BUSY_NIGHT'})[0].par_value;
                } else if (trans_type == 3) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_2_NO_ASW_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_3_NO_ASW_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosIntNoct, {par_name: 'GI_SEC_ACTION_3_BUSY_NIGHT'})[0].par_value;
                }
            } else if (listaName == 'accionesExtDiur') {
                if (trans_type == 1) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'CEL_ACTION_3_BUSY_DAY'})[0].par_value;
                } else if (trans_type == 2) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'ACTION_3_BUSY_DAY'})[0].par_value;
                } else if (trans_type == 3) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_1_DAY'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_2_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_2_BUSY_DAY'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_3_NO_ANSWER_DAY'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtDiur, {par_name: 'SEC_ACTION_3_BUSY_DAY'})[0].par_value;
                }
            } else if (listaName == 'accionesExtNoct') {
                if (trans_type == 1) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'CEL_ACTION_3_BUSY_NIGHT'})[0].par_value;
                } else if (trans_type == 2) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'ACTION_3_BUSY_NIGHT'})[0].par_value;
                } else if (trans_type == 3) {
                    lista[i].int_diur_accion1 = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_1_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_2_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion2Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_2_BUSY_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3NoReply = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_3_NO_ANSWER_NIGHT'})[0].par_value;
                    lista[i].int_diur_accion3Busy = $filter('filter')($scope.parametrosExtNoct, {par_name: 'SEC_ACTION_3_BUSY_NIGHT'})[0].par_value;
                }
            }
        }
    };

    // -- Valores por defecto de los DTMF
    $scope.actualizarDTMFDiurno = function() {
        $scope.phb_int_guide_number = $scope.phb_daytime_number;
        $scope.phb_ext_guide_number = $scope.phb_daytime_number;
    }
    $scope.actualizarDTMFNocturno = function() {
        $scope.phb_ext_guide_number = $scope.phb_daytime_number;
    }

    // -- Agrega una accion a su lista (vinculado al HTML)
    $scope.agregarAccion = function(lista, listaName) {
        if ($scope.parametrosIntDiur.length == 0 && listaName == 'accionesIntDiur') {
            $scope.transferenciaPredeterminadaChange(lista, listaName, doAgregarAccion);
        } else if ($scope.parametrosIntNoct.length == 0 && listaName == 'accionesIntNoct') {
            $scope.transferenciaPredeterminadaChange(lista, listaName, doAgregarAccion);
        } else if ($scope.parametrosExtDiur.length == 0 && listaName == 'accionesExtDiur') {
            $scope.transferenciaPredeterminadaChange(lista, listaName, doAgregarAccion);
        } else if ($scope.parametrosExtNoct.length == 0 && listaName == 'accionesExtNoct') {
            $scope.transferenciaPredeterminadaChange(lista, listaName, doAgregarAccion);
        } else {
            doAgregarAccion(lista, listaName);
        }
    };
    function doAgregarAccion(lista, listaName) {
        var data = {
            phb_trans_pred_int_diurno: null,
            phb_origin_number_int_diur: "",
            tap_phb_trans_type_int_diur: 2
        };

        data.int_diur_accion1 = null;
        data.int_diur_accion2NoReply = null;
        data.int_diur_accion2Busy = null;
        data.int_diur_accion3NoReply = null;
        data.int_diur_accion3Busy = null;
        data.int_diur_accion1_transfPhbId = null;
        data.int_diur_accion2NoReply_transfPhbId = null;
        data.int_diur_accion2Busy_transfPhbId = null;
        data.int_diur_accion3NoReply_transfPhbId = null;
        data.int_diur_accion3Busy_transfPhbId = null;
        lista.push(data);
        $scope.setTransferenciaPredeterminada ([data], listaName, data.tap_phb_trans_type_int_diur);
    }

    // -- Quitar una accion a su lista (vinculado al HTML)
    $scope.quitarAccion = function(accion, lista) {
        if (lista.length == 1) {
            UtilsService.showToast({delay: 5000, text: "Debe haber al menos un grupo de acciones"});
            return;
        }
        lista.splice(lista.indexOf(accion), 1);
    };

    // -- Validar que no se repita el numero de origen dentro de una lista
    function validarNroOrigen(index, nroOrigen, lista, destinoTransferencia) {
        var length = lista.length;
        for (var i=0; i<length; i++) {
            if (index != i) {
                if (lista[i].phb_origin_number_int_diur == nroOrigen 
                    && lista[i].tap_phb_trans_type_int_diur == destinoTransferencia) {
                    return false;
                }
            }
        }
        return true;
    }

    // -- Validar que el campo "Tansferir a" se encuentre completo
    // -- El 20180814 se quito esta validacion
    function validarYLimpiarTransferirA(lista) {
        if (lista.int_diur_accion1 == 7000) {
            if (angular.isUndefined(lista.int_diur_accion1_transfPhbId) || lista.int_diur_accion1_transfPhbId == null) {
                return false
            }
        } else {
            lista.int_diur_accion1_transfPhbId = null;
        }
        if (lista.int_diur_accion2NoReply == 7000) {
            if (angular.isUndefined(lista.int_diur_accion2NoReply_transfPhbId) || lista.int_diur_accion2NoReply_transfPhbId == null) {
                return false
            }
        } else {
            lista.int_diur_accion2NoReply_transfPhbId = null;
        }
        if (lista.int_diur_accion2Busy == 7000) {
            if (angular.isUndefined(lista.int_diur_accion2Busy_transfPhbId) || lista.int_diur_accion2Busy_transfPhbId == null) {
                return false
            }
        } else {
            lista.int_diur_accion2Busy_transfPhbId = null;
        }
        if (lista.int_diur_accion3NoReply == 7000) {
            if (angular.isUndefined(lista.int_diur_accion3NoReply_transfPhbId) || lista.int_diur_accion3NoReply_transfPhbId == null) {
                return false
            }
        } else {
            lista.int_diur_accion3NoReply_transfPhbId = null;
        }
        if (lista.int_diur_accion3Busy == 7000) {
            if (angular.isUndefined(lista.int_diur_accion3Busy_transfPhbId) || lista.int_diur_accion3Busy_transfPhbId == null) {
                return false
            }
        } else {
            lista.int_diur_accion3Busy_transfPhbId = null;
        }
        return true;
    }

    // -- Guardado de persona
    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.phb_first_name)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el primer nombre de la persona (solapa datos generales)"});
            return;
        }
        if (UtilsService.isEmpty($scope.phb_last_name1)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el primer apellido de la persona (solapa datos generales)"});
            return;
        }
        if (UtilsService.isEmpty($scope.phb_dep_id)) {
            UtilsService.showToast({delay: 5000, text: "Debe seleccionar el departamento (solapa datos generales)"});
            return;
        }
        if (UtilsService.isEmpty($scope.phb_daytime_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno diurno (solapa datos telefónicos)"});
            return;
        }
        if (UtilsService.isEmpty($scope.phb_nighttime_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno nocturno (solapa datos telefónicos)"});
            return;
        }
        if (UtilsService.isEmpty($scope.phb_fax_daytime) || $scope.phb_fax_daytime == "") {
            $scope.phb_fax_daytime = null;
        }
        if (UtilsService.isEmpty($scope.phb_fax_nighttime) || $scope.phb_fax_nighttime == "") {
            $scope.phb_fax_nighttime = null;
        }
        if (UtilsService.isEmpty($scope.phb_vma_daytime) || $scope.phb_vma_daytime == "") {
            $scope.phb_vma_daytime = null;
        }
        if (UtilsService.isEmpty($scope.phb_vma_nighttime) || $scope.phb_vma_nighttime == "") {
            $scope.phb_vma_nighttime = null;
        }
        if (UtilsService.isEmpty($scope.phb_confirmation)) {
            UtilsService.showToast({delay: 5000, text: "Debe seleccionar la confirmación (solapa reconocimiento de voz)"});
            return;
        }

        if ($scope.phb_int_guide_number == null || $scope.phb_int_guide_number == "") {
            $scope.actualizarDTMFDiurno();
        }
        if ($scope.phb_ext_guide_number == null || $scope.phb_ext_guide_number == "") {
            $scope.actualizarDTMFNocturno();
        }

        // -- Persona
        var data = {
            // Datos generales
            phb_id: $scope.phb_id,
            phb_name: $scope.phb_name,
            phb_email: $scope.phb_email,
            phb_first_name: $scope.phb_first_name,
            phb_last_name1: $scope.phb_last_name1,
            phb_nick_name: $scope.phb_nick_name,
            phb_dep_id: $scope.phb_dep_id,
            phb_middle_name: ($scope.phb_middle_name == null)? '' : $scope.phb_middle_name,
            phb_last_name2: ($scope.phb_last_name2 == null)? '' : $scope.phb_last_name2,
            phb_is_sec: $scope.phb_is_sec,
            phb_sec_id: $scope.phb_sec_id,
            phb_is_transf: $scope.phb_is_transf,

            // Datos telefonicos
            phb_daytime_number: $scope.phb_daytime_number,
            phb_daytime_cellular: $scope.phb_daytime_cellular,
            phb_ext_access_key: $scope.phb_ext_access_key,
            phb_fax_daytime: $scope.phb_fax_daytime,
            phb_fax_nighttime: $scope.phb_fax_nighttime,
            phb_vma_daytime: $scope.phb_vma_daytime,
            phb_vma_nighttime: $scope.phb_vma_nighttime,
            phb_nighttime_number: $scope.phb_nighttime_number,
            phb_nighttime_cellular: $scope.phb_nighttime_cellular,
            phb_dialpost_number_fax_daytime: $scope.phb_dialpost_number_fax_daytime,
            phb_dialpost_number_fax_nighttime: $scope.phb_dialpost_number_fax_nighttime,
            phb_dialpost_number_vma_daytime: $scope.phb_dialpost_number_vma_daytime,
            phb_dialpost_number_vma_nighttime: $scope.phb_dialpost_number_vma_nighttime,

            // Opciones avanzadas
            phb_ge_allow_playback_int_number: $scope.phb_ge_allow_playback_int_number,
            phb_gi_allow_playback_int_number: $scope.phb_gi_allow_playback_int_number,
            phb_int_guide_number: ($scope.phb_int_guide_number == null)? '' : $scope.phb_int_guide_number,
            phb_ext_guide_number: ($scope.phb_ext_guide_number == null)? '' : $scope.phb_ext_guide_number,
            phb_allow_htdf_ext: $scope.phb_allow_htdf_ext, // Flag
            phb_allow_htdf_int: $scope.phb_allow_htdf_int, // Flag
            phb_allow_voz_ext: $scope.phb_allow_voz_ext, // Flag
            phb_allow_voz_int: $scope.phb_allow_voz_int, // Flag
            phb_play_msg_info_tranf: $scope.phb_play_msg_info_tranf, // Flag
            phb_msg_sec_personal: $scope.phb_msg_sec_personal, // Flag

            // Reconocimiento de voz
            phb_fon_first_name: $scope.phb_fon_first_name,
            phb_fon_last_name1: $scope.phb_fon_last_name1,
            phb_confirmation: $scope.phb_confirmation,
            phb_fon_middle_name: $scope.phb_fon_middle_name,
            phb_fon_last_name2: $scope.phb_fon_last_name2,
            phb_grammar: $scope.phb_grammar
        };

        // -- Validacion Phb transfer options
        data.phb_transfer_options = [];
        var length = $scope.accionesIntDiur.length;
        if (length && $scope.accionesIntDiur[0].phb_trans_pred_int_diurno) {
        } else {
            for (var i=0; i<length; i++) {
                if (!validarNroOrigen(i, angular.isUndefined($scope.accionesIntDiur[i].phb_origin_number_int_diur)? "" : $scope.accionesIntDiur[i].phb_origin_number_int_diur, $scope.accionesIntDiur, $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur)) {
                    UtilsService.showToast({delay: 5000, text: "Clave duplicada en acciones para guía interna diurna"});
                    return;
                }
                /* if (!validarYLimpiarTransferirA($scope.accionesIntDiur[i])) {
                    UtilsService.showToast({delay: 5000, text: "Faltan definir datos de transferencia en la guía interna diurna"});
                    return;
                } */
            }
        }
        var length = $scope.accionesIntNoct.length;
        if (length && $scope.accionesIntNoct[0].phb_trans_pred_int_diurno) {
        } else {
            for (var i=0; i<length; i++) {
                if (!validarNroOrigen(i, angular.isUndefined($scope.accionesIntNoct[i].phb_origin_number_int_diur)? "" : $scope.accionesIntNoct[i].phb_origin_number_int_diur, $scope.accionesIntNoct, $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur)) {
                    UtilsService.showToast({delay: 5000, text: "Clave duplicada en acciones para guía interna nocturna"});
                    return;
                }
                /* if (!validarYLimpiarTransferirA($scope.accionesIntNoct[i])) {
                    UtilsService.showToast({delay: 5000, text: "Faltan definir datos de transferencia en la guía interna nocturna"});
                    return;
                } */
            }
        }
        var length = $scope.accionesExtDiur.length;
        if (length && $scope.accionesExtDiur[0].phb_trans_pred_int_diurno) {
        } else {
            for (var i=0; i<length; i++) {
                if (!validarNroOrigen(i, angular.isUndefined($scope.accionesExtDiur[i].phb_origin_number_int_diur)? "" : $scope.accionesExtDiur[i].phb_origin_number_int_diur, $scope.accionesExtDiur, $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur)) {
                    UtilsService.showToast({delay: 5000, text: "Clave duplicada en acciones para guía externa diurna"});
                    return;
                }
                /* if (!validarYLimpiarTransferirA($scope.accionesExtDiur[i])) {
                    UtilsService.showToast({delay: 5000, text: "Faltan definir datos de transferencia en la guía externa diurna"});
                    return;
                } */
            }
        }
        var length = $scope.accionesExtNoct.length;
        if (length && $scope.accionesExtNoct[0].phb_trans_pred_int_diurno) {
        } else {
            for (var i=0; i<length; i++) {
                if (!validarNroOrigen(i, angular.isUndefined($scope.accionesExtNoct[i].phb_origin_number_int_diur)? "" : $scope.accionesExtNoct[i].phb_origin_number_int_diur, $scope.accionesExtNoct, $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur)) {
                    UtilsService.showToast({delay: 5000, text: "Clave duplicada en acciones para guía externa nocturna"});
                    return;
                }
                /* if (!validarYLimpiarTransferirA($scope.accionesExtNoct[i])) {
                    UtilsService.showToast({delay: 5000, text: "Faltan definir datos de transferencia en la guía externa nocturna"});
                    return;
                } */
            }
        }

        // -- Armado de Grammar options
        data.phb_grammar_options = [];
        if (!angular.isUndefined($scope.phb_grammar_options_selected)) {
            var length = $scope.phb_grammar_options_selected.length;
            for (var i=0; i<length; i++) {
                data.phb_grammar_options.push($scope.phb_grammar_options_selected[i]);
            }
        }

        // -- Armado de Phb transfer options INT DIUR
        if ($scope.accionesIntDiur.length > 0 && !$scope.accionesIntDiur[0].phb_trans_pred_int_diurno) {
            var length = $scope.accionesIntDiur.length;
            for (var i=0; i<length; i++) {
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 0,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntDiur[i].int_diur_accion1,
                    tap_transf_phb_id: $scope.accionesIntDiur[i].int_diur_accion1_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 1,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesIntDiur[i].int_diur_accion2NoReply,
                    tap_transf_phb_id: $scope.accionesIntDiur[i].int_diur_accion2NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 1,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntDiur[i].int_diur_accion2Busy,
                    tap_transf_phb_id: $scope.accionesIntDiur[i].int_diur_accion2Busy_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 2,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesIntDiur[i].int_diur_accion3NoReply,
                    tap_transf_phb_id: $scope.accionesIntDiur[i].int_diur_accion3NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 2,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntDiur[i].int_diur_accion3Busy,
                    tap_transf_phb_id: $scope.accionesIntDiur[i].int_diur_accion3Busy_transfPhbId
                });
            }
        }

        // -- Armado de Phb transfer options INT NOCT
        if ($scope.accionesIntNoct.length > 0 && !$scope.accionesIntNoct[0].phb_trans_pred_int_diurno) {
            var length = $scope.accionesIntNoct.length;
            for (var i=0; i<length; i++) {
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 0,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntNoct[i].int_diur_accion1,
                    tap_transf_phb_id: $scope.accionesIntNoct[i].int_diur_accion1_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 1,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesIntNoct[i].int_diur_accion2NoReply,
                    tap_transf_phb_id: $scope.accionesIntNoct[i].int_diur_accion2NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 1,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntNoct[i].int_diur_accion2Busy,
                    tap_transf_phb_id: $scope.accionesIntNoct[i].int_diur_accion2Busy_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 2,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesIntNoct[i].int_diur_accion3NoReply,
                    tap_transf_phb_id: $scope.accionesIntNoct[i].int_diur_accion3NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: true,
                    tap_origin_number: $scope.accionesIntNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesIntNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 2,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesIntNoct[i].int_diur_accion3Busy,
                    tap_transf_phb_id: $scope.accionesIntNoct[i].int_diur_accion3Busy_transfPhbId
                });
            }
        }

        // -- Armado de Phb transfer options EXT DIUR
        if ($scope.accionesExtDiur.length > 0 && !$scope.accionesExtDiur[0].phb_trans_pred_int_diurno) {
            var length = $scope.accionesExtDiur.length;
            for (var i=0; i<length; i++) {
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 0,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtDiur[i].int_diur_accion1,
                    tap_transf_phb_id: $scope.accionesExtDiur[i].int_diur_accion1_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 1,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesExtDiur[i].int_diur_accion2NoReply,
                    tap_transf_phb_id: $scope.accionesExtDiur[i].int_diur_accion2NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 1,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtDiur[i].int_diur_accion2Busy,
                    tap_transf_phb_id: $scope.accionesExtDiur[i].int_diur_accion2Busy_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 2,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesExtDiur[i].int_diur_accion3NoReply,
                    tap_transf_phb_id: $scope.accionesExtDiur[i].int_diur_accion3NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtDiur[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtDiur[i].tap_phb_trans_type_int_diur,
                    tap_daytime: true,
                    tap_order: 2,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtDiur[i].int_diur_accion3Busy,
                    tap_transf_phb_id: $scope.accionesExtDiur[i].int_diur_accion3Busy_transfPhbId
                });
            }
        }

        // -- Armado de Phb transfer options EXT NOCT
        if ($scope.accionesExtNoct.length > 0 && !$scope.accionesExtNoct[0].phb_trans_pred_int_diurno) {
            var length = $scope.accionesExtNoct.length;
            for (var i=0; i<length; i++) {
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 0,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtNoct[i].int_diur_accion1,
                    tap_transf_phb_id: $scope.accionesExtNoct[i].int_diur_accion1_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 1,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesExtNoct[i].int_diur_accion2NoReply,
                    tap_transf_phb_id: $scope.accionesExtNoct[i].int_diur_accion2NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 1,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtNoct[i].int_diur_accion2Busy,
                    tap_transf_phb_id: $scope.accionesExtNoct[i].int_diur_accion2Busy_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 2,
                    tap_busy: false,
                    tap_tao_id: $scope.accionesExtNoct[i].int_diur_accion3NoReply,
                    tap_transf_phb_id: $scope.accionesExtNoct[i].int_diur_accion3NoReply_transfPhbId
                });
                data.phb_transfer_options.push({
                    tap_int_guide: false,
                    tap_origin_number: $scope.accionesExtNoct[i].phb_origin_number_int_diur,
                    tap_phb_transfer_type: $scope.accionesExtNoct[i].tap_phb_trans_type_int_diur,
                    tap_daytime: false,
                    tap_order: 2,
                    tap_busy: true,
                    tap_tao_id: $scope.accionesExtNoct[i].int_diur_accion3Busy,
                    tap_transf_phb_id: $scope.accionesExtNoct[i].int_diur_accion3Busy_transfPhbId
                });
            }
        }

        // -- Proceso de guardado
        PersonaService.guardar(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Persona guardada correctamente"});
                if (response.data.data !== undefined && response.data.data.id !== undefined) {
                    $mdDialog.hide({message: "OK", id: response.data.data.id});
                } else {
                    $mdDialog.hide({message: "OK", id: $scope.phb_id});
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

    // TODO: Obsoleto
    $scope.addEmail = function() {
        $scope.emails.push({
            value: $scope.phb_email_unitario
        });
        $scope.phb_email_unitario = "";
    };

    // TODO: Obsoleto
    $scope.removeEmail = function(item) {
        var index = $scope.emails.indexOf(item);
        if (index > -1) {
            $scope.emails.splice(index, 1);
        }
    };

    // TODO: Obsoleto
    function parseEmails() {
        if ($scope.emails.length > 1) {
            return $scope.emails.map(function(elem){
                return elem.value;
            }).join(";")
        } else if ($scope.emails.length == 1){
            return $scope.emails[0].value;
        } else {
            return "";
        }
    }

    // -- Redefinir en $scope la funcion del $rootScope para la barra de progreso
    $scope.getProgressBarClass = $rootScope.getProgressBarClass;

    // -- Redefinir en $scope la funcion del $rootScope para el checkbox
    $scope.getCheckboxClass = $rootScope.getCheckboxClass;

    // -- Carga de la Persona
    // $scope.emails = [];
    $scope.cargando = false;
    if (!angular.isUndefined(dialogData)) {
        var primeroIntDiur = true;
        var primeroIntNoct = true;
        var primeroExtDiur = true;
        var primeroExtNoct = true;
        var dataIntDiur = {};
        var dataIntNoct = {};
        var dataExtDiur = {};
        var dataExtNoct = {};

        // Obtener datos
        $scope.cargando = true;
        PersonaService.loadPersona(dialogData.phb_id).then(function(response){
            if (!angular.isUndefined(response.data)) {

                // Pasar datos obtenidos a $scope
                for (var name in response.data.data) {
                    $scope[name] = response.data.data[name];
                }
                if ($scope['phb_is_sec'] == "T") {
                    $scope['phb_is_sec'] = true;
                } else {
                    $scope['phb_is_sec'] = false;
                }
                if ($scope['phb_is_transf'] == "T") {
                    $scope['phb_is_transf'] = true;
                } else {
                    $scope['phb_is_transf'] = false;
                }
                $scope.titulo = response.data.data.phb_full_name;

                // Data de plantilla
                if (dialogData.isTemplate) {
                    delete $scope.phb_id;
                    delete $scope.phb_first_name;
                    delete $scope.phb_middle_name;
                    delete $scope.phb_nick_name;
                    delete $scope.phb_last_name1;
                    delete $scope.phb_last_name2;
                    delete $scope.phb_email;
                    delete $scope.phb_daytime_number;
                    delete $scope.phb_daytime_cellular;
                    delete $scope.phb_nighttime_number;
                    delete $scope.phb_nighttime_cellular;
                    $scope.titulo = "Nueva persona";
                }

                // Armado de grammar options
                $scope.phb_grammar_options_selected = [];
                if (!angular.isUndefined(response.data.data.phb_grammar_options)) {
                    var length = response.data.data.phb_grammar_options.length;
                    for (var i=0; i<length; i++) {
                        $scope.phb_grammar_options_selected.push(response.data.data.phb_grammar_options[i]);
                    }
                }

                // Armado de transfer options
                if (!angular.isUndefined(response.data.data.transfer_options) && response.data.data.transfer_options != null) {
                    var length = response.data.data.transfer_options.length;
                    for (var i = 0; i<length; i++) {
                        var transfer_options = response.data.data.transfer_options[i];
                        switch(UtilsService.evalAccion(transfer_options)) {
                            case "INT-DIUR":
                                if ($scope.accionesIntDiur.length > 0 && primeroIntDiur) {
                                    $scope.accionesIntDiur = [];
                                    primeroIntDiur = false;
                                }
                                if (angular.isUndefined(dataIntDiur.phb_trans_pred_int_diurno)) {
                                    dataIntDiur = {
                                        phb_trans_pred_int_diurno: false,
                                        phb_origin_number_int_diur: transfer_options.tap_origin_number == 'DEFAULT'? '' : transfer_options.tap_origin_number,
                                        tap_phb_trans_type_int_diur: UtilsService.getDestinoTransferencia(transfer_options.tap_phb_transfer_type)
                                    }
                                }
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        dataIntDiur.int_diur_accion1 = transfer_options.tap_tao_id;
                                        dataIntDiur.int_diur_accion1_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2":
                                        dataIntDiur.int_diur_accion2NoReply = transfer_options.tap_tao_id;
                                        dataIntDiur.int_diur_accion2NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2B":
                                        dataIntDiur.int_diur_accion2Busy = transfer_options.tap_tao_id;
                                        dataIntDiur.int_diur_accion2Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3":
                                        dataIntDiur.int_diur_accion3NoReply = transfer_options.tap_tao_id;
                                        dataIntDiur.int_diur_accion3NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3B":
                                        dataIntDiur.int_diur_accion3Busy = transfer_options.tap_tao_id;
                                        dataIntDiur.int_diur_accion3Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                }
                                if (!angular.isUndefined(dataIntDiur.int_diur_accion1) && !angular.isUndefined(dataIntDiur.int_diur_accion2NoReply) &&
                                    !angular.isUndefined(dataIntDiur.int_diur_accion2Busy) && !angular.isUndefined(dataIntDiur.int_diur_accion3NoReply) &&
                                    !angular.isUndefined(dataIntDiur.int_diur_accion3Busy)) {                                        
                                    $scope.accionesIntDiur.push(dataIntDiur);
                                    dataIntDiur = {};
                                }
                                break;
                            case "INT-NOCT":
                                if ($scope.accionesIntNoct.length > 0 && primeroIntNoct) {
                                    $scope.accionesIntNoct = [];
                                    primeroIntNoct = false;
                                }
                                if (angular.isUndefined(dataIntNoct.phb_trans_pred_int_diurno)) {
                                    dataIntNoct = {
                                        phb_trans_pred_int_diurno: false,
                                        phb_origin_number_int_diur: transfer_options.tap_origin_number == 'DEFAULT'? '' : transfer_options.tap_origin_number,
                                        tap_phb_trans_type_int_diur: UtilsService.getDestinoTransferencia(transfer_options.tap_phb_transfer_type)
                                    }
                                }
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        dataIntNoct.int_diur_accion1 = transfer_options.tap_tao_id;
                                        dataIntNoct.int_diur_accion1_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2":
                                        dataIntNoct.int_diur_accion2NoReply = transfer_options.tap_tao_id;
                                        dataIntNoct.int_diur_accion2NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2B":
                                        dataIntNoct.int_diur_accion2Busy = transfer_options.tap_tao_id;
                                        dataIntNoct.int_diur_accion2Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3":
                                        dataIntNoct.int_diur_accion3NoReply = transfer_options.tap_tao_id;
                                        dataIntNoct.int_diur_accion3NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3B":
                                        dataIntNoct.int_diur_accion3Busy = transfer_options.tap_tao_id;
                                        dataIntNoct.int_diur_accion3Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                }
                                if (!angular.isUndefined(dataIntNoct.int_diur_accion1) && !angular.isUndefined(dataIntNoct.int_diur_accion2NoReply) &&
                                    !angular.isUndefined(dataIntNoct.int_diur_accion2Busy) && !angular.isUndefined(dataIntNoct.int_diur_accion3NoReply) &&
                                    !angular.isUndefined(dataIntNoct.int_diur_accion3Busy)) {
                                    $scope.accionesIntNoct.push(dataIntNoct);
                                    dataIntNoct = {};
                                }
                                break;
                            case "EXT-DIUR":
                                if ($scope.accionesExtDiur.length > 0 && primeroExtDiur) {
                                    $scope.accionesExtDiur = [];
                                    primeroExtDiur = false;
                                }
                                if (angular.isUndefined(dataExtDiur.phb_trans_pred_int_diurno)) {
                                    dataExtDiur = {
                                        phb_trans_pred_int_diurno: false,
                                        phb_origin_number_int_diur: transfer_options.tap_origin_number == 'DEFAULT'? '' : transfer_options.tap_origin_number,
                                        tap_phb_trans_type_int_diur: UtilsService.getDestinoTransferencia(transfer_options.tap_phb_transfer_type)
                                    }
                                }
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        dataExtDiur.int_diur_accion1 = transfer_options.tap_tao_id;
                                        dataExtDiur.int_diur_accion1_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2":
                                        dataExtDiur.int_diur_accion2NoReply = transfer_options.tap_tao_id;
                                        dataExtDiur.int_diur_accion2NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2B":
                                        dataExtDiur.int_diur_accion2Busy = transfer_options.tap_tao_id;
                                        dataExtDiur.int_diur_accion2Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3":
                                        dataExtDiur.int_diur_accion3NoReply = transfer_options.tap_tao_id;
                                        dataExtDiur.int_diur_accion3NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3B":
                                        dataExtDiur.int_diur_accion3Busy = transfer_options.tap_tao_id;
                                        dataExtDiur.int_diur_accion3Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                }
                                if (!angular.isUndefined(dataExtDiur.int_diur_accion1) && !angular.isUndefined(dataExtDiur.int_diur_accion2NoReply) &&
                                    !angular.isUndefined(dataExtDiur.int_diur_accion2Busy) && !angular.isUndefined(dataExtDiur.int_diur_accion3NoReply) &&
                                    !angular.isUndefined(dataExtDiur.int_diur_accion3Busy)) {
                                    $scope.accionesExtDiur.push(dataExtDiur);
                                    dataExtDiur = {};
                                }
                                break;
                            case "EXT-NOCT":
                                if ($scope.accionesExtNoct.length > 0 && primeroExtNoct) {
                                    $scope.accionesExtNoct = [];
                                    primeroExtNoct = false;
                                }
                                if (angular.isUndefined(dataExtNoct.phb_trans_pred_int_diurno)) {
                                    dataExtNoct = {
                                        phb_trans_pred_int_diurno: false,
                                        phb_origin_number_int_diur: transfer_options.tap_origin_number == 'DEFAULT'? '' : transfer_options.tap_origin_number,
                                        tap_phb_trans_type_int_diur: UtilsService.getDestinoTransferencia(transfer_options.tap_phb_transfer_type)
                                    }
                                }
                                switch(UtilsService.getTipoDeAccion(transfer_options)) {
                                    case "A1":
                                        dataExtNoct.int_diur_accion1 = transfer_options.tap_tao_id;
                                        dataExtNoct.int_diur_accion1_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2":
                                        dataExtNoct.int_diur_accion2NoReply = transfer_options.tap_tao_id;
                                        dataExtNoct.int_diur_accion2NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A2B":
                                        dataExtNoct.int_diur_accion2Busy = transfer_options.tap_tao_id;
                                        dataExtNoct.int_diur_accion2Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3":
                                        dataExtNoct.int_diur_accion3NoReply = transfer_options.tap_tao_id;
                                        dataExtNoct.int_diur_accion3NoReply_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                    case "A3B":
                                        dataExtNoct.int_diur_accion3Busy = transfer_options.tap_tao_id;
                                        dataExtNoct.int_diur_accion3Busy_transfPhbId = transfer_options.tap_transf_phb_id;
                                        break;
                                }
                                if (!angular.isUndefined(dataExtNoct.int_diur_accion1) && !angular.isUndefined(dataExtNoct.int_diur_accion2NoReply) &&
                                    !angular.isUndefined(dataExtNoct.int_diur_accion2Busy) && !angular.isUndefined(dataExtNoct.int_diur_accion3NoReply) &&
                                    !angular.isUndefined(dataExtNoct.int_diur_accion3Busy)) {
                                    $scope.accionesExtNoct.push(dataExtNoct);
                                    dataExtNoct = {};
                                }
                                break;
                        }
                    }
                }
                $scope.cargando = false;
                initializing = false;
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cargar la persona"});
                return;
            }
        }, function() {
            UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cargar la persona"});
            return;
        });
    } else {
        $scope.titulo = "Nueva persona";

        // -- Carga de valores por defecto (no combos)
        $scope.phb_gi_allow_playback_int_number = false;
        $scope.phb_ge_allow_playback_int_number = false;
        $scope.phb_grammar_options_selected = [0];
        $scope.phb_allow_htdf_int = true;
        $scope.phb_allow_htdf_ext = true;
        $scope.phb_allow_voz_int = true;
        $scope.phb_allow_voz_ext = true;
    }

    // -- FIX access to root scope from modal
    $scope.getEnvironmentClass = function() {
        return $rootScope.getEnvironmentClass();
    }

});
