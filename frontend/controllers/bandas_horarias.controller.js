/**
 * BANDAS HORARIAS
 * Controller de las bandas horarias
 */
app.controller('BandasHorariasController', function ($scope, $http, UsuarioFactory, UtilsService, $rootScope, ParametroService, $filter, CONST) {

    // -- Armado de entorno
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("BandasHorarias", false, (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)));
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("BandasHorarias");
    }
    $scope.nombreEmpresa = UsuarioFactory.get().nombreEmpresa;
    $rootScope.nombreUsuario = UsuarioFactory.get().nombre;

    // -- Logo de la empresa
    if (UsuarioFactory.get().img != null) {
        $rootScope.setLogoEmpresa('assets/public/' + UsuarioFactory.get().img);
    }

    // -- Agregado de leyenda Plantilla de...
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.plantilla = "Plantilla de ";
    } else {
        $scope.plantilla = "";
    }

    // -- Context menu
    $scope.menuOptions = [
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.eliminarBandaHoraria($itemScope.banda);
            }
        }
    ];

    $scope.viewItem = function (dia) {
        console.log(di);
    }

    // -- Busqueda de registros
    $scope.selectedTab = 1;
    $scope.cargando = false;
    $scope.gridOptions = {
        data: [],
        urlSync: false
    };
    $scope.bandasHorarias = [];
    $scope.bandasHorariasXDia = [];
    function obtenerBandasHorarias() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        $scope.bandasHorarias = [];
        $scope.bandasHorariasXDia = [];
        ParametroService.getBandasHorarias().then(function (response) {
            var i = 0;
            for (i in response.data.data) {
                var item = response.data.data[i];
                item.ban_id = item.ban_day_type + "_" + item.ban_start_hour;

                // Valor
                var auxDecimalStart = 0, auxDecimalEnd = 0;
                if (item.ban_start_hour.toString().substr(2) == "30") {
                    auxDecimalStart = 0.5;
                } else if (item.ban_start_hour.toString().substr(2) == "15") {
                    auxDecimalStart = 0.25;
                } else if (item.ban_start_hour.toString().substr(2) == "45") {
                    auxDecimalStart = 0.75;
                }
                if (item.ban_end_hour.toString().substr(2) == "30") {
                    auxDecimalEnd = 0.5;
                } else if (item.ban_end_hour.toString().substr(2) == "15") {
                    auxDecimalEnd = 0.25;
                } else if (item.ban_end_hour.toString().substr(2) == "45") {
                    auxDecimalEnd = 0.75;
                }
                auxDecimalStart += item.ban_start_hour_value;
                auxDecimalEnd += ((item.ban_end_hour_value == 0) ? 24 : item.ban_end_hour_value);

                // Colspan
                item.col_span = auxDecimalEnd - auxDecimalStart;
                item.col_span *= 4;

                // Label y css
                item.css = item.ban_opm_code.toLowerCase();
                item.ban_start_hour_label = ('0000' + (item.ban_start_hour_value == 24 ? '00' : item.ban_start_hour_value)).slice(-2) + ":" + item.ban_start_hour.toString().substr(2);
                item.ban_end_hour_label = ('0000' + (item.ban_end_hour_value == 24 ? '24' : item.ban_end_hour_value)).slice(-2) + ":" + item.ban_end_hour.toString().substr(2);
                item.ban_start_hour_value_original = item.ban_start_hour_value;
                item.ban_end_hour_value_original = item.ban_end_hour_value;

                $scope.gridOptions.data.push(item);
                $scope.bandasHorarias.push(item);
            }

            $scope.bandasHorariasXDia['LUNES'] = $scope.obtenerBandaHorariaXDia("LUNES");
            $scope.bandasHorariasXDia['MARTES'] = $scope.obtenerBandaHorariaXDia("MARTES");
            $scope.bandasHorariasXDia['MIERCOLES'] = $scope.obtenerBandaHorariaXDia("MIERCOLES");
            $scope.bandasHorariasXDia['JUEVES'] = $scope.obtenerBandaHorariaXDia("JUEVES");
            $scope.bandasHorariasXDia['VIERNES'] = $scope.obtenerBandaHorariaXDia("VIERNES");
            $scope.bandasHorariasXDia['SABADO'] = $scope.obtenerBandaHorariaXDia("SABADO");
            $scope.bandasHorariasXDia['DOMINGO'] = $scope.obtenerBandaHorariaXDia("DOMINGO");
            $scope.bandasHorariasXDia['FERIADO'] = $scope.obtenerBandaHorariaXDia("FERIADO");
            $scope.bandasHorariasXDia['DEFECTO'] = $scope.obtenerBandaHorariaXDia("DEFECTO");
            $scope.cargando = false;
            $scope.states = $scope.loadAll();
        }, function () {
            $scope.cargando = false;
        });
    }
    obtenerBandasHorarias();

    $scope.obtenerBandaHorariaXDia = function (dia) {
        var result = [];
        var bandasFiltradas = $filter('filter')($scope.bandasHorarias, { ban_day_type: dia });
        if (!angular.isUndefined(bandasFiltradas) && bandasFiltradas.length > 0) {
            // Hora inicio > hora hasta (x lo gral nocturnos)
            // var add = null;
            // for (var i=0; i<bandasFiltradas.length; i++) {
            /*if (bandasFiltradas[i].ban_start_hour_value > bandasFiltradas[i].ban_end_hour_value) {
                add = angular.copy(bandasFiltradas[i]);
                bandasFiltradas[i].ban_end_hour_value = 24;
                bandasFiltradas[i].col_span = bandasFiltradas[i].ban_end_hour_value - bandasFiltradas[i].ban_start_hour_value;
                add.ban_start_hour_value = 0;
                add.col_span = add.ban_end_hour_value - add.ban_start_hour_value;
            }*/
            // }
            // if (add != null) {
            //    bandasFiltradas.push(add);
            // }

            // Hora inicio < hora hasta (x lo gral diurnos)
            var emptyReg = { col_span: 0, css: 'empty' };
            for (var i = 0; i < 96; i++) {
                var hayBandaHorariaParaLaHora = false;
                for (var j = 0; j < bandasFiltradas.length; j++) {
                    var auxValueEval = null;
                    var value = bandasFiltradas[j];
                    if (value.ban_start_hour.toString().substr(2) == "30") {
                        auxValueEval = value.ban_start_hour_value * 4;
                        auxValueEval++;
                        auxValueEval++;
                    } else if (value.ban_start_hour.toString().substr(2) == "15") {
                        auxValueEval = value.ban_start_hour_value * 4;
                        auxValueEval++;
                    } else if (value.ban_start_hour.toString().substr(2) == "45") {
                        auxValueEval = value.ban_start_hour_value * 4;
                        auxValueEval++;
                        auxValueEval++;
                        auxValueEval++;
                    } else {
                        auxValueEval = value.ban_start_hour_value * 4;
                    }
                    if (auxValueEval == i) {
                        if (emptyReg.col_span > 0) {
                            result.push(emptyReg);
                            emptyReg = { col_span: 0, css: 'empty' };
                        }
                        result.push(value);
                        i += value.col_span - 1;
                        hayBandaHorariaParaLaHora = true;
                    }
                }
                if (!hayBandaHorariaParaLaHora) {
                    emptyReg.col_span++;
                }
            }
        }

        // Ajuste
        var acum = 0
        angular.forEach(result, function (value, key) {
            acum += value.col_span;
        });
        if (acum < 96) {
            result.push({ col_span: 96 - acum, css: 'empty' });
        }
        return result;
    }

    $scope.horas = [];
    $scope.cargarHoras = function () {
        $scope.horas = [];
        for (var i = 0; i < 24; i++) {
            $scope.horas.push({ value: i, label: ('0000' + i).slice(-2) + ":" + "00" });
        }
    }
    $scope.cargarHoras();

    $scope.isSelected = function (item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.ban_id == $scope.idSeleccionado) {
            return true;
        }
        return false;
    }

    $scope.idSeleccionado = undefined;
    $scope.itemSeleccionado = undefined;
    $scope.viewItem = function (item) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.ban_id) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.ban_id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, { ban_id: item.ban_id })[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);
            }
        }
    }

    $scope.hidePreview = function () {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Banda horaria seleccionado";
        $scope.itemSeleccionado = undefined;
    }

    $scope.crearBandaHoraria = function () {
        UtilsService.showDialog("views/banda_horaria.html?_=" + new Date().getTime(), "BandaHorariaController").then(function (answer) {
            obtenerBandasHorarias();
        }, function () {

        });
    }

    $scope.editarBandaHoraria = function (banda, bandasDelDia) {

        var dialogData = {
            bandas: []
        };
        for (var i in bandasDelDia) {
            if (bandasDelDia[i].ban_day_type !== undefined) {
                dialogData.bandas.push(bandasDelDia[i]);
            }
        }
        if (dialogData.bandas.length > 0) {
            UtilsService.showDialog("views/banda_horaria.html?_=" + new Date().getTime(), "BandaHorariaController", dialogData).then(function (answer) {
                obtenerBandasHorarias();
                $scope.hidePreview();
            }, function () {

            });
        }
    }

    $scope.eliminarBandaHoraria = function (banda) {
        if (banda.ban_id !== undefined) {
            var mensaje = "";
            if (banda.ban_day_type == "DEFECTO") {
                mensaje = "Desea eliminar las bandas horarias por DEFECTO?";
            } else {
                mensaje = "Desea eliminar las bandas horarias los días " + banda.ban_day_type + "?";
            }
            UtilsService.showConfirm("Atención!", mensaje).then(function () {
                ParametroService.eliminarBandaHorariaPorDia(banda.ban_day_type).then(function successCallback(response) {
                    if (response.data.code == 1) {
                        UtilsService.showToast({ delay: 5000, text: "Las bandas horarias fueron eliminadas correctamente" });
                        obtenerBandasHorarias();
                    } else {
                        UtilsService.showToast({ delay: 5000, text: "Ocurrió un problema al eliminar las bandas horarias" });
                    }
                }, function errorCallback(error) {
                    UtilsService.showToast({ delay: 5000, text: UtilsService.processError(error) });
                });
            });
        }
    }

    function hayVacios(dia) {
        var result = $filter('filter')($scope.bandasHorariasXDia[dia], { css: "empty" });
        if (!angular.isUndefined(result) && result.length > 0) {
            return true;
        }
        return false;
    }

    /* inicio BUSQUEDA DINAMICA */
    $scope.loadAll = function () {
        allStates = [];
        for (var i = 0; i < $scope.gridOptions.data.length; i++) {
            allStates.push($scope.gridOptions.data[i]);
            allStates[i].value = $scope.gridOptions.data[i].ban_day_type + " - " + $scope.gridOptions.data[i].ban_opm_code;
            allStates[i].display = $scope.gridOptions.data[i].ban_day_type + " - " + $scope.gridOptions.data[i].ban_opm_code;
        }
        return allStates;
    }

    $scope.querySearch = function (query) {
        var result = $filter('filter')($scope.states, { value: query });
        return result;
    }

    $scope.searchTextChange = function (text) {

    }

    $scope.selectedItemChange = function (item) {
        $scope.viewItem(item);
    }
    /* fin BUSQUEDA DINAMICA */

});


/**
 * BANDA HORARIA - DIALOG
 * Controller del dialog de la banda horaria seleccionada
 */
app.controller('BandaHorariaController', function ($scope, $http, UsuarioFactory, UtilsService, $mdDialog, ParametroService, dialogData, $timeout, CONST) {

    $scope.titulo = "Nueva banda horaria";

    $scope.cancelar = function () {
        $mdDialog.cancel();
    }

    $scope.opcionesComplemento = CONST.COMPLEMENTS;
    $scope.ban_complement = $scope.opcionesComplemento[0].value;

    //Range slider with ticks and values
    $scope.slider = {
        minValue: 0,
        maxValue: 24,
        options: {
            ceil: 24,
            floor: 0,
            showTicksValues: true
        }
    };
    $timeout(function () {
        $scope.$broadcast('rzSliderForceRender');
    }, 500);

    ParametroService.getDayTypes().then(function (response) {
        $scope.dayTypes = response.data.data;
    });

    $scope.aceptar = function () {
        if (UtilsService.isEmpty($scope.ban_day_type)) {
            UtilsService.showToast({ delay: 5000, text: "Debe ingresar el día" });
            return;
        }

        var bandasAGuardar = [];
        var output = $scope.dateSlider.noUiSlider.get();
        for (var i = 1; i < output.length; i += 2) {
            // End
            var auxTime = parseInt(output[i]);
            // FIX 24hs
            if (auxTime == 547873200000) {
                $scope.ban_end_hour = "2400";
            } else {
                auxTime = new Date(auxTime);
                $scope.ban_end_hour = "";
                if (auxTime.getHours() < 10) {
                    $scope.ban_end_hour += "0" + auxTime.getHours();
                } else {
                    $scope.ban_end_hour += auxTime.getHours();
                }
                if (auxTime.getMinutes() < 10) {
                    $scope.ban_end_hour += "0" + auxTime.getMinutes();
                } else {
                    $scope.ban_end_hour += auxTime.getMinutes();
                }
            }

            // Start
            var j = i;
            j--;
            auxTime = parseInt(output[j]);
            auxTime = new Date(auxTime);
            $scope.ban_start_hour = "";
            if (auxTime.getHours() < 10) {
                $scope.ban_start_hour += "0" + auxTime.getHours();
            } else {
                $scope.ban_start_hour += auxTime.getHours();
            }
            if (auxTime.getMinutes() < 10) {
                $scope.ban_start_hour += "0" + auxTime.getMinutes();
            } else {
                $scope.ban_start_hour += auxTime.getMinutes();
            }

            var data = {
                ban_day_type: $scope.ban_day_type,
                ban_start_hour: $scope.ban_start_hour,
                ban_opm_code: $scope.handlesOpmCodes[i], /*$scope.ban_opm_code,*/
                ban_end_hour: $scope.ban_end_hour,
                ban_description: $scope.ban_description,
                ban_complement: $scope.ban_complement
            }
            if (!UtilsService.isEmpty(dialogData)) {
                data.ban_id = $scope.ban_id;
                data.ban_day_type_old = $scope.ban_day_type_old;
                data.ban_start_hour_old = $scope.ban_start_hour_old;
            }
            bandasAGuardar.push(data);
        }

        // Complemento
        var bandasComplementariasAGuardar = $scope.completarConElComplemento(bandasAGuardar);
        bandasAGuardar = bandasAGuardar.concat(bandasComplementariasAGuardar);

        ParametroService.guardarBandasHorarias(bandasAGuardar).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({ delay: 5000, text: "Banda horaria guardada correctamente" });
                $mdDialog.hide("OK");
            } else {
                UtilsService.showToast({ delay: 5000, text: "Ocurrió un problema al guardar la banda horaria" });
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({ delay: 5000, text: UtilsService.processError(error) });
        });
    }

    // $scope.bandaHorariaCompleta24HS = false;
    $scope.handles = [];
    $scope.handlesConnect = [];
    $scope.handlesOpmCodes = [];
    $scope.handlesOpmCodesOli = [];

    $scope.setOpmCodeOfHandle = function(desde, hasta, opmCode) {
        $scope.handlesOpmCodesOli.push({
            value: opmCode,
            desde: desde,
            hasta: hasta
        });
    }

    $scope.updateHandleOfOpmCode = function(valuesOnStart, valuesOnEnd, handle) {
        
        // Si es par es el DESDE
        if (handle%2 == 0) {
            for (var i in $scope.handlesOpmCodesOli) {
                if ($scope.handlesOpmCodesOli[i].desde == valuesOnStart[handle] && $scope.handlesOpmCodesOli[i].hasta == valuesOnStart[handle + 1]) {
                    $scope.handlesOpmCodesOli[i].desde =  valuesOnEnd[handle];
                    $scope.handlesOpmCodesOli[i].hasta =  valuesOnEnd[handle + 1];
                }
            }
        } else {
            for (var i in $scope.handlesOpmCodesOli) {
                if ($scope.handlesOpmCodesOli[i].desde == valuesOnStart[handle - 1] && $scope.handlesOpmCodesOli[i].hasta == valuesOnStart[handle]) {
                    $scope.handlesOpmCodesOli[i].desde =  valuesOnEnd[handle - 1];
                    $scope.handlesOpmCodesOli[i].hasta =  valuesOnEnd[handle];
                }
            }
        }
  
    }

    $scope.updateValueOfOpmCode = function(desde, hasta, opmCodeNuevo) {
        for (var i in $scope.handlesOpmCodesOli) {
            if ($scope.handlesOpmCodesOli[i].desde == desde && $scope.handlesOpmCodesOli[i].hasta == hasta) {
                $scope.handlesOpmCodesOli[i].value = opmCodeNuevo;
            }
        }
    }

    $scope.getOpmCodeFromHandle = function(desde, hasta) {
        for (var i in $scope.handlesOpmCodesOli) {
            if ($scope.handlesOpmCodesOli[i].desde == desde && $scope.handlesOpmCodesOli[i].hasta == hasta) {
                return $scope.handlesOpmCodesOli[i].value;
            }
        }
    }

    $scope.removeOpmCodeFromHandle = function(desde, hasta) {
        var newHandlesOpmCodesOli = [];
        for (var i in $scope.handlesOpmCodesOli) {
            if ($scope.handlesOpmCodesOli[i].desde != desde && $scope.handlesOpmCodesOli[i].hasta != hasta) {
                newHandlesOpmCodesOli.push($scope.handlesOpmCodesOli[i]);
            }
        }
        $scope.handlesOpmCodesOli = newHandlesOpmCodesOli;
    }

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Editar banda horaria";
        $scope.ban_day_type = dialogData.bandas[0].ban_day_type;

        var handleToAdd = null;
        var handleDesdeAdded = null;
        var handleHastaAdded = null;
        // var ultimaHoraAgregada = null;
        // $scope.bandaHorariaCompleta24HS = true;
        for (var i in dialogData.bandas) {
            handleToAdd = UtilsService.HHMMToTimeStamp(dialogData.bandas[i].ban_start_hour);
            $scope.handles.push(handleToAdd);
            handleDesdeAdded = new Number(handleToAdd).valueOf();

            handleToAdd = UtilsService.HHMMToTimeStamp(dialogData.bandas[i].ban_end_hour);
            // FIX, se agrega un dia si la hora fin es 0000
            if (dialogData.bandas[i].ban_end_hour == "0000") {
                handleToAdd = new Date(handleToAdd + 1 * 24 * 60 * 60 * 1000);
            }
            $scope.handles.push(handleToAdd);
            handleHastaAdded = new Number(handleToAdd).valueOf();

            if (i > 0) {
                $scope.handlesConnect.push(true, false);
            } else {
                $scope.handlesConnect = [
                    false,
                    true,
                    false
                ];
            }
            $scope.handlesOpmCodes.push(dialogData.bandas[i].ban_opm_code);
            $scope.handlesOpmCodes.push(dialogData.bandas[i].ban_opm_code);
            $scope.setOpmCodeOfHandle(handleDesdeAdded, handleHastaAdded, dialogData.bandas[i].ban_opm_code);

            /* if (dialogData.bandas[i].ban_end_hour) {
                if (ultimaHoraAgregada == null) {
                    ultimaHoraAgregada = dialogData.bandas[i].ban_end_hour
                } else {
                    if (ultimaHoraAgregada != dialogData.bandas[i].ban_start_hour) {
                        $scope.bandaHorariaCompleta24HS = false;
                    } else {
                        ultimaHoraAgregada = dialogData.bandas[i].ban_end_hour;
                    }
                }
            } */
        }

        /* $scope.ban_day_type = dialogData.ban_day_type;
        $scope.ban_opm_code = dialogData.ban_opm_code;
        $scope.ban_complement = dialogData.ban_complement;
        $scope.ban_description = dialogData.ban_description;
        $scope.ban_id = dialogData.ban_id;
        $scope.ban_day_type_old = angular.copy(dialogData.ban_day_type);
        $scope.ban_start_hour_old = angular.copy(dialogData.ban_start_hour);

        if (dialogData.ban_start_hour_value_original > dialogData.ban_end_hour_value_original) {
            $scope.slider.minValue = dialogData.ban_end_hour_value_original;
            $scope.slider.maxValue = dialogData.ban_start_hour_value_original;
            $scope.ban_opm_code = CONST.OPERATION_MODES[0].opm_code; // DIURNO
        } else {
            $scope.slider.minValue = dialogData.ban_start_hour_value_original;
            $scope.slider.maxValue = dialogData.ban_end_hour_value_original;
        } */
    } else {
        $scope.handles = [
            new Date(1987, 4, 12, 00, 00, 00).getTime(),
            new Date(1987, 4, 12, 02, 00, 00).getTime()
        ];
        $scope.handlesConnect = [
            false,
            true,
            false
        ];
        $scope.handlesOpmCodes = [
            CONST.OPERATION_MODES[0].opm_code,
            CONST.OPERATION_MODES[0].opm_code
        ];
        $scope.setOpmCodeOfHandle(new Date(1987, 4, 12, 00, 00, 00).getTime(), new Date(1987, 4, 12, 02, 00, 00).getTime(), CONST.OPERATION_MODES[0].opm_code);
    }

    $scope.completarConElComplemento = function (bandasAGuardar) {
        var bandasComplementariasAGuardar = [];
        if ($scope.ban_complement != CONST.COMPLEMENTS[0].value) {
            for (var i = 0; i < bandasAGuardar.length; i++) {
                if (i == 0) {
                    if (bandasAGuardar[i].ban_start_hour != "0000") {
                        var bandaComplementaria = {
                            ban_day_type: $scope.ban_day_type,
                            ban_start_hour: "0000",
                            ban_opm_code: ($scope.ban_complement == 2) ? "DIURNO" : "NOCTURNO",
                            ban_end_hour: bandasAGuardar[i].ban_start_hour,
                            ban_description: $scope.ban_description,
                            ban_complement: $scope.ban_complement
                        }
                        bandasComplementariasAGuardar.push(bandaComplementaria);
                    }
                }

                if (i + 1 < bandasAGuardar.length) {
                    if (bandasAGuardar[i].ban_end_hour != bandasAGuardar[i + 1].ban_start_hour) {
                        var bandaComplementaria = {
                            ban_day_type: $scope.ban_day_type,
                            ban_start_hour: bandasAGuardar[i].ban_end_hour,
                            ban_opm_code: ($scope.ban_complement == 2) ? "DIURNO" : "NOCTURNO",
                            ban_end_hour: bandasAGuardar[i + 1].ban_start_hour,
                            ban_description: $scope.ban_description,
                            ban_complement: $scope.ban_complement
                        }
                        bandasComplementariasAGuardar.push(bandaComplementaria);
                    }
                } else {
                    if (bandasAGuardar[i].ban_end_hour != "0000") {
                        var bandaComplementaria = {
                            ban_day_type: $scope.ban_day_type,
                            ban_start_hour: bandasAGuardar[i].ban_end_hour,
                            ban_opm_code: ($scope.ban_complement == 2) ? "DIURNO" : "NOCTURNO",
                            ban_end_hour: "0000",
                            ban_description: $scope.ban_description,
                            ban_complement: $scope.ban_complement
                        }
                        bandasComplementariasAGuardar.push(bandaComplementaria);
                    }
                }
            }
        }
        return bandasComplementariasAGuardar;
    }

    // -- Slider noUiSlider
    $scope.dateSlider = {};
    $(document).ready(function () {
        $scope.dateSlider = document.getElementById('slider');

        $('#agregarRangoHorario').click(function () {
            if ($scope.handles.length >= 10) {
                UtilsService.showToast({ delay: 5000, text: "Se alcanzó el límite de intervalos dentro de una banda horaria" });
                return;
            }
            var rangoHorario = $scope.obtenerRangoHorarioLibre();
            $scope.handles.push(rangoHorario.desde);
            $scope.handles.push(rangoHorario.hasta);
            $scope.handlesConnect.push(true, false);
            $scope.handlesOpmCodes.push(CONST.OPERATION_MODES[0].opm_code);
            $scope.handlesOpmCodes.push(CONST.OPERATION_MODES[0].opm_code);
            $scope.setOpmCodeOfHandle(new Number(rangoHorario.desde).valueOf(), new Number(rangoHorario.hasta).valueOf(), CONST.OPERATION_MODES[0].opm_code);
            $scope.dateSlider.noUiSlider.destroy()
            $scope.handles = $scope.handles.sort();
            setupSlider();
        });

        $('#agregarRangoHorarioNocturno').click(function () {
            if ($scope.handles.length >= 10) {
                UtilsService.showToast({ delay: 5000, text: "Se alcanzó el límite de intervalos dentro de una banda horaria" });
                return;
            }
            var rangoHorario = $scope.obtenerRangoHorarioLibre();
            $scope.handles.push(rangoHorario.desde);
            $scope.handles.push(rangoHorario.hasta);
            $scope.handlesConnect.push(true, false);
            $scope.handlesOpmCodes.push(CONST.OPERATION_MODES[1].opm_code);
            $scope.handlesOpmCodes.push(CONST.OPERATION_MODES[1].opm_code);
            $scope.setOpmCodeOfHandle(new Number(rangoHorario.desde).valueOf(), new Number(rangoHorario.hasta).valueOf(), CONST.OPERATION_MODES[1].opm_code);
            $scope.dateSlider.noUiSlider.destroy()
            $scope.handles = $scope.handles.sort();
            setupSlider();
        });

        $scope.obtenerRangoHorarioLibre = function () {
            var rangoOfrecido = (3600000 * 2);
            var rangoSeparacion = (3600000);
            var min = 547786800000;
            var max = 547873200000;
            var desde = min;
            var hasta = max + rangoOfrecido;

            var auxIntervalosConMinYMax = [];
            auxIntervalosConMinYMax.push(min);
            for (var i in $scope.handles) {
                auxIntervalosConMinYMax.push($scope.handles[i]);
            }
            auxIntervalosConMinYMax.push(max);

            for (var i = min; i <= max; i = i + (rangoOfrecido + rangoSeparacion)) {
                desde = i;
                hasta = desde + rangoOfrecido;
                for (var j = 0; j < auxIntervalosConMinYMax.length; j += 2) {
                    if (desde >= auxIntervalosConMinYMax[j] && hasta <= auxIntervalosConMinYMax[j+1]) {
                        rangoHorarioLibre = {
                            desde: desde,
                            hasta: hasta,
                        };
                        return rangoHorarioLibre;
                    }
                }
            }

            var rangoHorarioLibre = {
                desde: new Date(1987, 4, 12, 20, 00, 00).getTime(),
                hasta: new Date(1987, 4, 12, 22, 00, 00).getTime(),
            }
            return rangoHorarioLibre;
        }

        $('#evaluarBandas').click(function () {
            var output = $scope.dateSlider.noUiSlider.get();
            console.log(output);

            $scope.processCSS();
        });

        function setupSlider() {
            noUiSlider.create($scope.dateSlider, {
                range: {
                    min: new Date(1987, 4, 12, 00, 00, 00).getTime(),
                    max: new Date(1987, 4, 13, 00, 00, 00).getTime(),
                },

                step: 15 * 60 * 1000,

                start: $scope.handles,

                behaviour: 'drag',
                connect: $scope.handlesConnect,

                // tooltips: true,
                // format: { to: toFormat, from: Number },

                pips: {
                    mode: 'steps',
                    filter: function (value, type) {
                        var auxTime = new Date(value);
                        if (auxTime.getMinutes() == 0) {
                            return 2;
                        }
                        return 0;
                    },
                    format: {
                        to: function (value) {
                            // FIX 24hs
                            if (value == 547873200000) {
                                return "24:00";
                            } else {
                                return UtilsService.timeStampToHHMM(value);
                            }
                        },
                        from: function (value) {
                            console.log(value);
                            return UtilsService.timeStampToHHMM(value);
                        }
                    }
                }
            });

            $scope.dateSlider.noUiSlider.on('end', function (values, handle) {
                var aux = [];
                for (var i in values) {
                    aux.push(parseInt(values[i]));
                }
                $scope.handles = aux;
            });

            var valuesOnStart = [];
            var valuesOnEnd = [];
            // Se ejecuta primero
            $scope.dateSlider.noUiSlider.on('start', function (values, handle) {
                valuesOnStart = values;
                $('.hora-min-label').fadeTo("fast", 1);
            });

            // Se ejecuta segundo
            $scope.dateSlider.noUiSlider.on('change', function (values, handle) {
                valuesOnEnd = values;
            });

            // Se ejecuta tercero. 
            // Si se mueve una franja entera se llama 2 veces
            // En caso que se mueva solo un handle se llama 1 vez
            var franjaSeleccionada = [];
            var franjaSeleccionadaFires = [];
            $scope.dateSlider.noUiSlider.on('end', function (values, handle) {
                $scope.updateHandleOfOpmCode(valuesOnStart, valuesOnEnd, handle);
                $('.hora-min-label').fadeTo(200, 0);
                var hayCambios = false;
                for (var i in valuesOnStart) {
                    if (valuesOnStart[i] != valuesOnEnd[i]) {
                        hayCambios = true;
                        break;
                    }
                }
                if (!hayCambios) {
                    if (franjaSeleccionada.length < 2) {
                        franjaSeleccionada.push(handle);
                        franjaSeleccionadaFires.push(new Date().getTime());
                        if (franjaSeleccionada.length == 2) {
                            if (franjaSeleccionadaFires[0] + 250 >= franjaSeleccionadaFires[1]) {
                                var dialogData2 = {
                                    // opm_code: $scope.handlesOpmCodes[handle],
                                    opm_code: $scope.getOpmCodeFromHandle($scope.handles[franjaSeleccionada[0]], $scope.handles[franjaSeleccionada[1]]),
                                    start: $scope.handles[franjaSeleccionada[0]],
                                    end: $scope.handles[franjaSeleccionada[1]],
                                    cantidadHandlesActual: $scope.handles.length
                                }
                                UtilsService.showDialog("views/banda_horaria_detalle.html?_=" + new Date().getTime(), "BandaHorariaDetalleController", dialogData2, true).then(function (response) {
                                    if (response == "ELIMINAR") {
                                        var newHandles = [];
                                        var newHandlesOpmCodes = [];
                                        for (var i in $scope.handles) {
                                            if (i != franjaSeleccionada[0] && i != franjaSeleccionada[1]) {
                                                newHandles.push($scope.handles[i]);
                                            }
                                        }
                                        for (var i in $scope.handlesOpmCodes) {
                                            if (i != franjaSeleccionada[0] && i != franjaSeleccionada[1]) {
                                                newHandlesOpmCodes.push($scope.handlesOpmCodes[i]);
                                            }
                                        }
                                        $scope.removeOpmCodeFromHandle($scope.handles[franjaSeleccionada[0]], $scope.handles[franjaSeleccionada[1]]);
                                        $scope.handles = newHandles;
                                        $scope.handlesOpmCodes = newHandlesOpmCodes;
                                        $scope.handles = $scope.handles.sort();
                                        $scope.handlesConnect = [
                                            false,
                                            true,
                                            false
                                        ];
                                        for (var i in $scope.handles) {
                                            if (i >= 1) {
                                                if (i%2 == 0) {
                                                    $scope.handlesConnect.push(true);
                                                    $scope.handlesConnect.push(false);
                                                }
                                            }
                                        }
                                        $scope.dateSlider.noUiSlider.destroy()
                                        setupSlider();
                                    } else {
                                        $scope.handlesOpmCodes[franjaSeleccionada[0]] = response;
                                        $scope.handlesOpmCodes[franjaSeleccionada[1]] = response;
                                        $scope.updateValueOfOpmCode($scope.handles[franjaSeleccionada[0]], $scope.handles[franjaSeleccionada[1]], response);
                                    }
                                    // $scope.bandaHorariaCompleta24HS = false;
                                    $scope.processCSS();
                                    franjaSeleccionada = [];
                                    franjaSeleccionadaFires = [];
                                }, function () {
                                    franjaSeleccionada = [];
                                    franjaSeleccionadaFires = [];
                                });
                                return;
                            }
                            franjaSeleccionada = [];
                            franjaSeleccionadaFires = [];
                        }
                    }
                }
            });

            // Se ejecuta on drag
            $('.hora-min-label').fadeTo("fast", 0);
            $scope.dateSlider.noUiSlider.on('update', function (values, handle) {
                if ((handle + 1) % 2 == 0) {
                    var horaMinDragStart = "";
                    if (parseInt(values[handle - 1]) == 547873200000) {
                        horaMinDragStart = "24:00";
                    } else {
                        horaMinDragStart = UtilsService.timeStampToHHMM(parseInt(values[handle - 1]));
                    }

                    var horaMinDragEnd = "";
                    if (parseInt(values[handle]) == 547873200000) {
                        horaMinDragEnd = "24:00";
                    } else {
                        horaMinDragEnd = UtilsService.timeStampToHHMM(parseInt(values[handle]));
                    }

                    document.getElementById('horaMinDragStart').innerHTML = horaMinDragStart;
                    document.getElementById('horaMinDragEnd').innerHTML = horaMinDragEnd;
                } else {
                    var horaMinDragStart = "";
                    if (parseInt(values[handle]) == 547873200000) {
                        horaMinDragStart = "24:00";
                    } else {
                        horaMinDragStart = UtilsService.timeStampToHHMM(parseInt(values[handle]));
                    }

                    var horaMinDragEnd = "";
                    if (parseInt(values[handle + 1]) == 547873200000) {
                        horaMinDragEnd = "24:00";
                    } else {
                        horaMinDragEnd = UtilsService.timeStampToHHMM(parseInt(values[handle + 1]));
                    }

                    document.getElementById('horaMinDragStart').innerHTML = horaMinDragStart;
                    document.getElementById('horaMinDragEnd').innerHTML = horaMinDragEnd;
                }
                $scope.mostrarHoraMinDrag = true;
            });

            $scope.processCSS = function () {
                var connect = slider.querySelectorAll('.noUi-connect');
                for (var i = 0; i < connect.length; i++) {
                    connect[i].classList.remove("dialog-franja-diurno");
                    connect[i].classList.remove("dialog-franja-nocturno");
                    // i * 2 devuelve el DESDE de la franja que se esta recorriendo
                    var opmCode = $scope.getOpmCodeFromHandle($scope.handles[i * 2], $scope.handles[(i * 2) + 1]);
                    // if ($scope.handlesOpmCodes[i * 2] == "DIURNO") {
                    if (opmCode == "DIURNO") {
                        connect[i].classList.add("dialog-franja-diurno");
                    } else {
                        connect[i].classList.add("dialog-franja-nocturno");
                    }
                }
            }
            $scope.processCSS();
        }

        setupSlider();
    });

});


/**
 * BANDA HORARIA SELECCION MODO - DIALOG
 * Controller del dialog del selector del modo de la banda horaria seleccionada
 */
app.controller('BandaHorariaDetalleController', function ($scope, $http, UsuarioFactory, UtilsService, $mdDialog, ParametroService, dialogData, $timeout, CONST) {

    $scope.titulo = "Intervalo " + UtilsService.timeStampToHHMM(dialogData.start) + "hs a " + UtilsService.timeStampToHHMM(dialogData.end) + "hs";

    ParametroService.getOperationModes().then(function (response) {
        $scope.operationModes = response.data.data;
        $scope.ban_opm_code = dialogData.opm_code;
    });

    $scope.puedeEliminar = (dialogData.cantidadHandlesActual != 2);

    $scope.aceptar = function () {
        $mdDialog.hide($scope.ban_opm_code);
    }

    $scope.cancelar = function () {
        $mdDialog.cancel();
    }

    $scope.eliminar = function () {
        $mdDialog.hide("ELIMINAR");
    }

});