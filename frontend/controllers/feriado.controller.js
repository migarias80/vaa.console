/**
 * FERIADO
 * Controller del listado de feriados
 */
app.controller('FeriadosController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, ParametroService, $filter, CONST) {

    // -- Armado de entorno
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Feriados", false, (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)));
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Feriados");
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

    // -- Agregado de leyenda Plantilla de
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.plantilla = "Plantilla de ";
    } else {
        $scope.plantilla = "";
    }

    $scope.selectedTab = 1;
    $scope.cargando = false;

    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'hol_date_value',
            direction: 'asc'
        }
    };
    function obtenerFeriados() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        ParametroService.getFeriados().then(function(response){
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            var encontrado = false;
            angular.forEach(response.data.data, function(value, key) {
                value.hol_date_value = new Date(value.hol_date);
                if (isNaN(value.hol_date_value.getTime())) {
                    var aux = value.hol_date.split(" ");
                    aux = aux[0];
                    aux = aux.split("-");
                    value.hol_date_value = new Date(aux[0], aux[1]-1, aux[2]);
                }
                $scope.gridOptions.data.push(value);
                if (reemplazarItemSeleccionado) {
                    if (value.hol_date == $scope.itemSeleccionado.hol_date) {
                        $scope.viewItem(value, true, false);
                        encontrado = true;
                    }
                }
            });
            $scope.cargando = false;
            if (reemplazarItemSeleccionado && !encontrado) {
                $scope.hidePreview();
            }

            $scope.states = $scope.loadAll();
        }, function() {
            $scope.cargando = false;
        });
    }
    obtenerFeriados();

    $scope.getNombreItemSeleccionado = function() {
        if (angular.isUndefined($scope.itemSeleccionado)) {
            return "Feriado seleccionado";
        } else {
            return $scope.itemSeleccionado.hol_description;
        }
    };

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.hol_date == $scope.idSeleccionado) {
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
                $scope.editarFeriado($itemScope.item);
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.eliminarFeriado($itemScope.item);
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
    $scope.viewItem = function(item, forceView) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.hol_date && !forceView) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.hol_date;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, {hol_date: item.hol_date})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);
            }
        }
    };

    $scope.hidePreview = function() {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Feriado seleccionado";
        $scope.itemSeleccionado = undefined;
        $scope.searchText = "";
    };

    $scope.crearFeriado = function() {
        UtilsService.showDialog("views/feriado.html", "FeriadoController").then(function(answer) {
            obtenerFeriados();
        }, function() {

        });
    };

    $scope.editarFeriado = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/feriado.html", "FeriadoController", item).then(function(answer) {
                obtenerFeriados();
            }, function() {

            });
        }
    };

    $scope.eliminarFeriado = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.display + "?").then(function() {
                ParametroService.eliminarFeriado(item.hol_date).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({delay: 5000, text: item.display + " fue eliminado correctamente"});
                        obtenerFeriados();
                    } else {
                        UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.display});
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
            allStates[i].value = $filter('date')($scope.gridOptions.data[i].hol_date_value, "dd/MM/yyyy") + " - " + $scope.gridOptions.data[i].hol_description;
            allStates[i].display = $filter('date')($scope.gridOptions.data[i].hol_date_value, "dd/MM/yyyy") + " - " + $scope.gridOptions.data[i].hol_description;
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
        $scope.viewItem(item);
    };

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});


/**
 * FERIADO - DIALOG
 * Controller del dialog del feriado seleccionado
 */
app.controller('FeriadoController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, ParametroService, dialogData, $filter, $mdDateLocale) {

    $scope.titulo = "Nuevo Feriado";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.hol_description)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la descripción del feriado"});
            return;
        }
        if (UtilsService.isEmpty($scope.hol_date)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la fecha del feriado"});
            return;
        }

        var dias = ['DOMINGO', 'LUNES',  'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];
        var data = {
            hol_description: $scope.hol_description,
            hol_date: $scope.hol_date,
            hol_date_old: $scope.hol_date_old,
            hol_day_type: dias[$scope.hol_date.getDay()]
        }
        ParametroService.guardarFeriado(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Feriado guardado correctamente"});
                $mdDialog.hide("OK");
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar el feriado"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Editar Feriado";
        $scope.hol_date = dialogData.hol_date_value;
        $scope.hol_date_old =  angular.copy(dialogData.hol_date_value);
        $scope.hol_description = dialogData.hol_description;
    }

    // -- Formatear fechas de inputs date
    $mdDateLocale.formatDate = function(date, timezone) {
        if (!date) {
            return '';
        }

        var localeTime = date.toLocaleTimeString();
        var formatDate = date;
        if (date.getHours() === 0 && (localeTime.indexOf('11:') !== -1 || localeTime.indexOf('23:') !== -1)) {
            formatDate = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 1, 0, 0);
        }
        
        return $filter('date')(formatDate, 'd/M/yyyy', timezone);
    }

});