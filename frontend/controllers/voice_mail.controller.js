/**
 * VMA
 * Controller del listado de voice mails
 */
app.controller('VoiceMailsController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, VoiceMailService, $filter, CONST) {

    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Voices", false);
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Voices");
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
            predicate: 'vma_description',
            direction: 'asc'
        }
    };
    function obtenerVoiceMails() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        VoiceMailService.getVoiceMail().then(function(response){
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            angular.forEach(response.data.data, function(value, key) {
                $scope.gridOptions.data.push(value);
                if (reemplazarItemSeleccionado) {
                    if (value.vma_id == $scope.itemSeleccionado.vma_id) {
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
    obtenerVoiceMails();

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.vma_id == $scope.idSeleccionado) {
            return true;
        }
        return false;
    };

    // -- Context menu
    $scope.menuOptions = [
        {
            text: 'Editar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.editarVMA($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.eliminarVMA($itemScope.item);
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
            if ($scope.idSeleccionado == item.vma_id && !forceView) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.vma_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, {vma_id: item.vma_id})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);

                if (focusItem) {
                    setTimeout(function() {
                        var indexTr = $("tr").index($('.item-seleccionado'));
                        indexTr --;
                        indexTr *= document.getElementsByClassName('tr-datos')[0].offsetHeight;
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
        $scope.nombreSeleccionado = "Voice mail seleccionado";
        $scope.itemSeleccionado = undefined;
        $scope.searchText = "";
    };

    $scope.crearVMA = function() {
        UtilsService.showDialog("views/voice_mail.html?_=" + new Date().getTime(), "VoiceMailController").then(function(response) {
            $scope.idSeleccionado = response.id;
            $scope.itemSeleccionado = {vma_id: response.id};
            obtenerVoiceMails();
        }, function() {

        });
    };

    $scope.editarVMA = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/voice_mail.html?_=" + new Date().getTime(), "VoiceMailController", item).then(function(answer) {
                obtenerVoiceMails();
            }, function() {

            });
        }
    };

    $scope.eliminarVMA = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.vma_description + "?").then(function() {
                VoiceMailService.eliminar(item.vma_id).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.vma_description + " fue eliminado correctamente"});
                        obtenerVoiceMails();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.vma_description});
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
            allStates[i].value = $scope.gridOptions.data[i].vma_description + " - " + $scope.gridOptions.data[i].vma_internal_number;
            allStates[i].display = $scope.gridOptions.data[i].vma_description;
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
        $scope.viewItem(item);
    };

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});


/**
 * VMA - DIALOG
 * Controller del dialog del vma seleccionado
 */
app.controller('VoiceMailController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, VoiceMailService, dialogData, $rootScope) {

    $scope.titulo = "Nuevo voice mail";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.vma_description)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la descripción del voice mail"});
            return;
        }
        if (UtilsService.isEmpty($scope.vma_internal_number)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el número de interno del voice mail"});
            return;
        }
        if ($scope.vma_allow_dial_post) {
            if (UtilsService.isEmpty($scope.vma_allow_dial_post)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar el número posterior"});
                return;
            }
            if (UtilsService.isEmpty($scope.vma_default_dialed_number)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar el número por omisión"});
                return;
            }
        }

        var data = {
            vma_id: $scope.vma_id,
            vma_description: $scope.vma_description,
            vma_internal_number: $scope.vma_internal_number,
            vma_enabled_daytime: $scope.vma_enabled_daytime,
            vma_enabled_nighttime: $scope.vma_enabled_nighttime,
            vma_allow_dial_post: $scope.vma_allow_dial_post,
            vma_default_dialed_number: $scope.vma_default_dialed_number,
            vma_digits: $scope.vma_digits
        }
        VoiceMailService.guardar(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Voice mail guardado correctamente"});
                if (response.data.data !== undefined && response.data.data.id !== undefined) {
                    $mdDialog.hide({message: "OK", id: response.data.data.id});
                } else {
                    $mdDialog.hide({message: "OK", id: $scope.vma_id});
                }
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar el voice mail"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Voice mail " + dialogData.vma_description;
        $scope.vma_id = dialogData.vma_id;
        $scope.vma_description = dialogData.vma_description;
        $scope.vma_enabled_daytime = dialogData.vma_enabled_daytime;
        $scope.vma_enabled_nighttime = dialogData.vma_enabled_nighttime;
        $scope.vma_allow_dial_post = dialogData.vma_allow_dial_post;
        $scope.vma_default_dialed_number = dialogData.vma_default_dialed_number;
        $scope.vma_digits = dialogData.vma_digits;
        $scope.vma_internal_number = dialogData.vma_internal_number;
    }

    // -- Redefinir en $scope la funcion del $rootScope para el checkbox
    $scope.getCheckboxClass = $rootScope.getCheckboxClass;

});