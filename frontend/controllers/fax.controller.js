/**
 * FAXES
 * Controller del listado de faxes
 */
app.controller('FaxesController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, FaxService, $filter, CONST) {

    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Faxes", false);
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Faxes");
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

    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'fax_description',
            direction: 'asc'
        }
    };
    function obtenerFaxes() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        FaxService.getFax().then(function(response){
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            angular.forEach(response.data.data, function(value, key) {
                $scope.gridOptions.data.push(value);
                if (reemplazarItemSeleccionado) {
                    if (value.fax_id == $scope.itemSeleccionado.fax_id) {
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
    obtenerFaxes();

    $scope.getNombreItemSeleccionado = function() {
        if (angular.isUndefined($scope.itemSeleccionado)) {
            return "Fax seleccionado";
        } else {
            return $scope.itemSeleccionado.fax_description;
        }
    };

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.fax_id == $scope.idSeleccionado) {
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
                $scope.editarFax($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.eliminarFax($itemScope.item, true, false);
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
    $scope.viewItem = function(item, forceView, focusItem) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.fax_id && !forceView) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.fax_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, {fax_id: item.fax_id})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);

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
        }
    };

    $scope.hidePreview = function() {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Fax seleccionado";
        $scope.itemSeleccionado = undefined;
        $scope.searchText = "";
    };

    $scope.crearFax = function() {
        UtilsService.showDialog("views/fax.html?_=" + new Date().getTime(), "FaxController").then(function(response) {
            $scope.idSeleccionado = response.id;
            $scope.itemSeleccionado = {fax_id: response.id};
            obtenerFaxes();
        }, function() {

        });
    };

    $scope.editarFax = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/fax.html?_=" + new Date().getTime(), "FaxController", item).then(function(answer) {
                obtenerFaxes();
            }, function() {

            });
        }
    };

    // -- Eliminar fax
    $scope.eliminarFax = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.fax_description + "?").then(function() {
                FaxService.eliminar(item.fax_id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.fax_description + " fue eliminado correctamente"});
                        obtenerFaxes();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.fax_description});
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                });

            });
        }
    };

    // -- Buqueda dinamica
    $scope.loadAll = function () {
        allStates = [];
        for (var i=0; i<$scope.gridOptions.data.length; i++){
            allStates.push($scope.gridOptions.data[i]);
            allStates[i].value = $scope.gridOptions.data[i].fax_description + " - " + $scope.gridOptions.data[i].fax_internal_number;
            allStates[i].display = $scope.gridOptions.data[i].fax_description;
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
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});


/**
 * FAX - DIALOG
 * Controller del dialog del fax seleccionado
 */
app.controller('FaxController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, FaxService, dialogData, $rootScope) {

    $scope.titulo = "Nuevo fax";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.fax_description)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la descripción del fax"});
            return;
        }
        if (UtilsService.isEmpty($scope.fax_internal_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno del fax"});
            return;
        }
        if ($scope.fax_allow_dial_post) {
            if (UtilsService.isEmpty($scope.fax_allow_dial_post)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar el número posterior"});
                return;
            }
            if (UtilsService.isEmpty($scope.fax_default_dialed_number)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar el número por omisión"});
                return;
            }
        }

        var data = {
            fax_id: $scope.fax_id,
            fax_description: $scope.fax_description,
            fax_internal_number: $scope.fax_internal_number,
            fax_enabled_daytime: $scope.fax_enabled_daytime,
            fax_enabled_nighttime: $scope.fax_enabled_nighttime,
            fax_allow_dial_post: $scope.fax_allow_dial_post,
            fax_default_dialed_number: $scope.fax_default_dialed_number,
            fax_digits: $scope.fax_digits
        }
        FaxService.guardar(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Fax guardado correctamente"});
                if (response.data.data !== undefined && response.data.data.id !== undefined) {
                    $mdDialog.hide({message: "OK", id: response.data.data.id});
                } else {
                    $mdDialog.hide({message: "OK", id: $scope.fax_id});
                }
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar el fax"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Fax " + dialogData.fax_description;
        $scope.fax_id = dialogData.fax_id;
        $scope.fax_description = dialogData.fax_description;
        $scope.fax_enabled_daytime = dialogData.fax_enabled_daytime;
        $scope.fax_enabled_nighttime = dialogData.fax_enabled_nighttime;
        $scope.fax_allow_dial_post = dialogData.fax_allow_dial_post;
        $scope.fax_default_dialed_number = dialogData.fax_default_dialed_number;
        $scope.fax_digits = dialogData.fax_digits;
        $scope.fax_internal_number = dialogData.fax_internal_number;
    }

    // -- Redefinir en $scope la funcion del $rootScope para el checkbox
    $scope.getCheckboxClass = $rootScope.getCheckboxClass;

});