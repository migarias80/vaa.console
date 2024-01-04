/**
 * ESTADISTICA
 * Controller de estadisticas
 */
app.controller('EstadisticaController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, EstadisticaService, $filter, CONST, Excel, $timeout, CSV, $mdDateLocale) {

    // -- Configuracion incial de la pantalla
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Estadisticas", false, (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)));
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Estadisticas");
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

    // -- Titulo de la pantalla
    $scope.titulo = "Estadísticas";
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.titulo = "Estadísticas Generales";
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

    $scope.selectedTab = 1;
    $scope.cargando = false;

    var hoy = new Date();
    var unMesAtras = new Date();
    unMesAtras = new Date(unMesAtras.setMonth(hoy.getMonth() - 1));
    $scope.fecha_desde = unMesAtras;
    $scope.fecha_hasta = hoy;

    $scope.showGridTroncalesLlamadas = false;
    $scope.showGridTroncales = false;
    $scope.showGridTroncalesMaximos = false;
    $scope.puedeConsultarCantidadLlamadas = true;
    
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.puedeConsultarCantidadLlamadas = false;
    }

    $scope.fechaHastaChange = function() {
        if ($scope.showGridLlamadas) {
            $scope.getLlamadas();
        } else if ($scope.showGridTroncales) {
            $scope.getTroncales();
        } else if ($scope.showGridTroncalesMaximos) {
            $scope.getTroncalesMaximos();
        }
    }

    $scope.fechaDesdeChange = function() {
        if ($scope.showGridLlamadas) {
            $scope.getLlamadas();
        } else if ($scope.showGridTroncales) {
            $scope.getTroncales();
        } else if ($scope.showGridTroncalesMaximos) {
            $scope.getTroncalesMaximos();
        }
    }
    
    // -- Obtener llamadas
    $scope.gridOptionsLlamadas = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'dar_date_value',
            direction: 'asc'
        }
    };
    $scope.getLlamadas = function(){
        $scope.cargando = true;
        $scope.gridOptionsLlamadas.data = [];
        $scope.showGridTroncalesMaximos = false;
        $scope.showGridTroncales = false;
        
        var data = {
            fechaDesde: dateToString($scope.fecha_desde),
            fechaHasta: dateToString($scope.fecha_hasta)
        }
        var ultimaFecha = null;
        var parImpar = false;
        EstadisticaService.getLlamadas(data).then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                if (ultimaFecha == null) {
                    ultimaFecha = value.dar_date;
                }
                if (ultimaFecha != value.dar_date) {
                    ultimaFecha = value.dar_date;
                    parImpar = !parImpar;
                }
                value.par_impar = parImpar;
                $scope.gridOptionsLlamadas.data.push(value);
            });

            $scope.showGridLlamadas = true;
            $scope.cargando = false;
        }, function() {
            $scope.cargando = false;
        });
    }    

    // -- Obtener troncales por dia
    $scope.gridOptionsTroncales = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'dar_date_value',
            direction: 'asc'
        }
    };
    $scope.getTroncales = function(){
        $scope.cargando = true;
        $scope.gridOptionsTroncales.data = [];
        $scope.showGridLlamadas = false;
        $scope.showGridTroncalesMaximos = false;
        
        var data = {
            fechaDesde: dateToString($scope.fecha_desde),
            fechaHasta: dateToString($scope.fecha_hasta)
        }
        var ultimaFecha = null;
        var parImpar = false;
        EstadisticaService.getTroncales(data).then(function(response) {
            angular.forEach(response.data.data, function(value, key) {
                value.mostrarBotonGrafico = false;
                if (ultimaFecha == null) {
                    ultimaFecha = value.dar_date;
                    value.mostrarBotonGrafico = true;
                }
                if (ultimaFecha != value.dar_date) {
                    ultimaFecha = value.dar_date;
                    parImpar = !parImpar;
                    value.mostrarBotonGrafico = true;
                }
                value.par_impar = parImpar;
                value.porcentaje_en_el_dia = value.porcentaje_en_el_dia.toString().replace(".", ",");
                $scope.gridOptionsTroncales.data.push(value);
            });

            $scope.showGridTroncales = true;
            $scope.cargando = false;
        }, function() {
            $scope.cargando = false;
        });
    }

    // -- Obtener troncales maximos
    $scope.gridOptionsTroncalesMaximos = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'dar_date_value',
            direction: 'asc'
        }
    };
    $scope.getTroncalesMaximos = function(){
        $scope.cargando = true;
        $scope.gridOptionsTroncalesMaximos.data = [];
        $scope.showGridLlamadas = false;
        $scope.showGridTroncales = false;
        
        var data = {
            fechaDesde: dateToString($scope.fecha_desde),
            fechaHasta: dateToString($scope.fecha_hasta)
        }
        var ultimaFecha = null;
        var parImpar = false;
        EstadisticaService.getTroncalesMaximos(data).then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                if (ultimaFecha == null) {
                    ultimaFecha = value.dar_date;
                }
                if (ultimaFecha != value.dar_date) {
                    ultimaFecha = value.dar_date;
                    parImpar = !parImpar;
                }
                value.par_impar = parImpar;
                $scope.gridOptionsTroncalesMaximos.data.push(value);
            });
            
            $scope.showGridTroncalesMaximos = true;
            $scope.cargando = false;
        }, function() {
            $scope.cargando = false;
        });
    }

    // -- Estilos para grilla de cantidad de llamadas
    $scope.getClassCantidadDeLlamadas = function(item) {
        var clase = ''
        if (item === undefined) {
            return clase;
        }
        if (item.frd_id == 1000 || item.frd_id == 2000 || item.frd_id == 3000) {
            clase += 'tr-bold';
        }
        if (item.frd_id == 3000) {
            clase += ' tr-alert';
        }
        if (item.par_impar) {
            clase += ' tr-impar';
        } else {
            clase += ' tr-par';
        }
        return clase;
    }

    // -- Estilos para grilla de tiempo de ocupacion
    $scope.getClassTiempoDeOcupacion = function(item) {
        var clase = ''
        if (item === undefined) {
            return clase;
        }
        if (item.existe_fdr_id_3000 == 1) {
            clase += ' tr-bold';
            clase += ' tr-alert';
        }
        if (item.par_impar) {
            clase += ' tr-impar';
        } else {
            clase += ' tr-par';
        }
        return clase;
    }

    // -- Estilos para grilla de uso maximo de troncales
    $scope.getClassUsoMaximoDeTroncales = function(item) {
        var clase = ''
        if (item === undefined) {
            return clase;
        }
        if (item.existe_fdr_id_3000 == 1) {
            clase += ' tr-bold';
            clase += ' tr-alert';
        }
        if (item.par_impar) {
            clase += ' tr-impar';
        } else {
            clase += ' tr-par';
        }
        return clase;
    }

    if ($scope.puedeConsultarCantidadLlamadas) {
        $scope.getLlamadas();
    } else {
        $scope.getTroncales();
    }

    $scope.exportToExcel = function(tableId) {
        var exportHref = Excel.tableToExcel(tableId,'Estadisticas');
        $timeout(function(){
            location.href=exportHref;
        }, 100);
    }

    $scope.exportToCsv = function(tableId, fileName) {
        if (angular.isUndefined(UsuarioFactory.get().urlEmpresa)) {
            fileName += "_general";
            CSV.tableToCsv(tableId, fileName);
        } else {
            fileName += "_" + UsuarioFactory.get().urlEmpresa;
            CSV.tableToCsv(tableId, fileName);
        }
    }

    $scope.verGraficoDetalleTroncal = function(item) {
        $scope.cargandoGrafico = true;

        var fecha = item.dar_date.split("/");
        fecha = new Date(fecha[2], fecha[1] - 1, fecha[0]);
        fecha = dateToString(fecha);
        
        var data = {
            fecha: fecha
        }
        EstadisticaService.getDetalleTroncal(data).then(function(response){
            var dialogData = {
                data: response.data.data,
                titulo: "Tiempo de ocupación del día " + item.dar_date,
                tipoGrafico: 1,
                tipoChart: "chart-bar"
            }
            UtilsService.showDialog("views/estadisticas_grafico.html?_=" + new Date().getTime(), "EstadisticaGraficoController", dialogData).then(function(answer) {
                $scope.hidePreview();
            }, function() {

            });
            $scope.cargandoGrafico = false;
        }, function() {
            $scope.cargandoGrafico = false;
        });
    }

    $scope.verGraficoTroncalesMaximos = function() {
        $scope.cargandoGrafico = true;

        var dialogData = {
            data: $scope.gridOptionsTroncalesMaximos.data,
            titulo: "Uso de máximo de troncales",
            tipoGrafico: 2,
            tipoChart: "chart-line"
        }

        UtilsService.showDialog("views/estadisticas_grafico.html?_=" + new Date().getTime(), "EstadisticaGraficoController", dialogData).then(function(answer) {
            $scope.hidePreview();
        }, function() {

        });

        $scope.cargandoGrafico = false;
    }

    // -- Pasar de date fecha a AAAAMMDD para llamar a los servicios
    function dateToString(date) {
        var mm = date.getMonth() + 1;
        var dd = date.getDate();
      
        return [date.getFullYear(),
                (mm>9 ? '' : '0') + mm,
                (dd>9 ? '' : '0') + dd
               ].join('');
    };

});


/**
 * ESTADISTICA - DIALOG
 * Controller del dialog de graficos de estadisticas
 */
app.controller('EstadisticaGraficoController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, ParametroService, dialogData, $filter) {

    $scope.titulo = dialogData.titulo;

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.aceptar = function() {
        $mdDialog.cancel();
    }

    $scope.estadisticas_labels = [];
    $scope.estadisticas_series = [];
    $scope.estadisticas_data = [];

    // -- Tipo de grafico a mostrar
    $scope.tipoChart = "chart-line";
    if (dialogData.tipoChart !== undefined) {
        $scope.tipoChart = dialogData.tipoChart;
    }

    // -- Grafico de detalle de troncal en un dia
    if (dialogData.tipoGrafico == 1) {
        var data = [];
        for (var i in dialogData.data) {
            $scope.estadisticas_labels.push(dialogData.data[i].frd_id);
            data.push(dialogData.data[i].porcentaje_en_el_dia);
        }
        $scope.estadisticas_data = [
            data
        ];
        $scope.estadisticas_series.push("Tiempo de ocupación");

        $scope.estadisticas_options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            },
            showTooltips: true,
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index];
                        var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        return datasetLabel + '%';
                    }
                }
            },
            animation: {
                onComplete : function () {
                    var chartInstance = this.chart,
                        ctx = chartInstance.ctx;
                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillStyle = "#666";

                    this.data.datasets.forEach(function (dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index] +'%';                            
                            ctx.fillText(data, bar._model.x, bar._model.y - 5);
                        });
                    });
                }
            }
        };

    // -- Grafico de troncales maximos entre fechas
    } else if (dialogData.tipoGrafico == 2) {
        var data = [];
        for (var i in dialogData.data) {
            $scope.estadisticas_labels.push($filter('date')(dialogData.data[i].dar_date, 'dd/MM/yyyy'));
            data.push(parseInt(dialogData.data[i].max_troncales_usadas));
        }
        $scope.estadisticas_data = [
            data
        ];
        $scope.estadisticas_series.push("Tiempo de ocupación");
    
        $scope.estadisticas_options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true,
                        callback: function(value) {if (value % 1 === 0) {return value;}}
                    }
                }]
            }
        };
    }
    
});