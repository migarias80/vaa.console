var app = angular.module("VaaApp", [
    'ngRoute',
    'ngAnimate',
    'ngMaterial',
    'angular-md5',
    'ngAnimate',
    'dataGrid',
    'pagination',
    'ui.rCalendar',
    'rzModule',
    'chart.js',
    'ui.bootstrap.contextMenu',
    'ngPatternRestrict'
]);

app.config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            controller: 'LoginController',
            templateUrl: 'views/login.html?_=1'
        })
        .when('/home', {
            controller: 'HomeController',
            templateUrl: 'views/home.html?_=' + new Date(),
            reload: true
        })
        .when('/home-eds', {
            controller: 'HomeEDSController',
            templateUrl: 'views/home_eds.html?_=1'
        })
        .when('/usuarios', {
            controller: 'UsuariosController',
            templateUrl: 'views/usuarios.html?_=1'
        })
        .when('/faxes', {
            controller: 'FaxesController',
            templateUrl: 'views/faxes.html?_=1'
        })
        .when('/voice_mails', {
            controller: 'VoiceMailsController',
            templateUrl: 'views/voice_mails.html?_=1'
        })
        .when('/departamentos', {
            controller: 'DepartamentosController',
            templateUrl: 'views/departamentos.html?_=1'
        })
        .when('/personas', {
            controller: 'PersonasController',
            templateUrl: 'views/personas.html?_=1'
        })
        .when('/bandas_horarias', {
            controller: 'BandasHorariasController',
            templateUrl: 'views/bandas_horarias.html?_=1'
        })
        .when('/feriados', {
            controller: 'FeriadosController',
            templateUrl: 'views/feriados.html?_=1'
        })
        .when('/parametros', {
            controller: 'ParametrosController',
            templateUrl: 'views/parametros.html?_=1'
        })
        .when('/estadisticas', {
            controller: 'EstadisticaController',
            templateUrl: 'views/estadisticas.html?_=1'
        })
        .when('/archivos', {
            controller: 'ArchivosController',
            templateUrl: 'views/archivos.html?_=1'
        })
        .when('/login/:urlempresa', {
            controller: 'LoginController',
            templateUrl: 'views/login.html?_=1'
        })
        .when('/login', {
            controller: 'LoginController',
            templateUrl: 'views/login.html?_=1'
        })
        .when('/logout', {
            controller: 'LogoutController',
            templateUrl: 'views/logout.html?_=1'
        })
        .when('/session_expired', {
            controller: 'SessionExpiredController',
            templateUrl: 'views/logout.html?_=1'
        })
        .otherwise({
            redirectTo: '/'
        });
});

app.config(['$httpProvider', function ($httpProvider) {
    $httpProvider.interceptors.push(['$q', 'UsuarioFactory', '$rootScope', function ($q, UsuarioFactory, $rootScope) {
        var service = {
            request: request,
            requestError: requestError,
            response: response,
            responseError: responseError
        };
        return service;

        // On request
        function request(config) {
            return config;
        }

        // On request error
        function requestError(rejection) {
            return $q.reject(rejection);
        }

        // On response
        function response(response) {
            // Acciones para cuando se recibe la respuesta
            if (!angular.isUndefined(response.data) && response.data.code == 3) {
                $rootScope.goto('session_expired');
                return;
            }
            if (!angular.isUndefined(response.headers('Authorization')) && response.headers('Authorization') != null && response.headers('Authorization') != '') {
                UsuarioFactory.setToken(response.headers('Authorization'));
            }
            return response;
        }

        // On error response
        function responseError(rejection) {
            if (!angular.isUndefined(rejection.data) && rejection.data != null && rejection.data.code == 3) {
                $rootScope.goto('session_expired');
                return;
            } else {
                return $q.reject(rejection);
            }
        }
    }]);
}]);

app.run(function ($window, $rootScope, $location, $http, $mdSidenav, $mdMenu, UsuarioFactory, CONST, UtilsService, UsuarioService) {

    $rootScope.goto = function (path) {
        if (path == 'home-eds') {
            UsuarioFactory.clearEmpresa();
            $rootScope.resetLogoEmpresa();
            UsuarioFactory.setEmpresa(undefined, CONST.ENVIRONMENT.EDS_ID);
        }
        $location.path('/' + path);
    };

    // -- Intro.js
    $( document ).ready(function() {
        $rootScope.inciarPaseoGuiado = function() {
            var intro = introJs();
            intro.setOptions({
                steps: [
                    {
                        intro: "<span class='tour-title'>Le damos la bienvenida a la consola de administración del VAA.</span></br></br>Lo invitamos a un breve recorrido por sus acciones principales"
                    },
                    {
                        intro: "Acceso a crear, modificar y buscar sus departamentos.",
                        element: document.querySelector('#btnDepartamentos')
                    },
                    {
                        intro: "Acceso a crear, modificar y buscar las personas dentro de cada departamento.",
                        element: document.querySelector('#btnPersonas')
                    },
                    {
                        intro: "Acceso a crear, modificar y buscar los faxes que serán utilizados dentro de sus departamentos y personas.",
                        element: document.querySelector('#btnFaxes')
                    },
                    {
                        intro: "Acceso a crear, modificar y buscar los faxes que serán utilizados dentro de sus departamentos y personas.",
                        element: document.querySelector('#btnVoiceMails')
                    },
                    {
                        intro: "Acceso a crear, modificar y listar las bandas horarias de su empresa.",
                        element: document.querySelector('#btnBandasHorarias')
                    },
                    {
                        intro: "Acceso a crear, modificar y listar los días no laborables de su empresa.",
                        element: document.querySelector('#btnFeriados')
                    },
                    {
                        intro: "Acceso a parámetros de configuración del funcionamiento del VAA. Solo debe modificar sus valores si está seguro de hacerlo.",
                        element: document.querySelector('#btnParametros')
                    },
                    {
                        intro: "Acceso a configuración de sus datos personales, cambio de contraseña y cierre de sesión.",
                        element: document.querySelector('#btnMisDatos')
                    },
                    {
                        intro: "Acceso a crear, modificar y listar los usuarios de la consola del VAA.",
                        element: document.querySelector('#btnGestionUsuarios')
                    }
                ],
                nextLabel: "Siguiente",
                prevLabel: "Anterior",
                skipLabel: "Cerrar",
                doneLabel: "Cerrar"
            });
            intro.start();
        }
    });

    $rootScope.verMisDatos = function () {
        UsuarioService.getUsuarioByToken().then(function(response){
            var dialogData = {
                id: response.data.data.id,
                nombreUsuario: UsuarioFactory.get().nombre,
                fullName: UsuarioFactory.get().nombreFull,
                idProfile: UsuarioFactory.get().idProfile,
                fromVerMisDatos: true
            };
            UtilsService.showDialog("views/usuario.html", "UsuarioController", dialogData).then(function(answer) {
                UtilsService.showToast({delay: 5000, text: "Tus datos serán reflejados a partir del próximo ingreso"});
            }, function() {

            });
        }, function() {
            UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
        });
    };

    $rootScope.cambiarMiPassword = function () {
        UsuarioService.getUsuarioByToken().then(function(response){
            var dialogData = {
                id: response.data.data.id,
                nombreUsuario: UsuarioFactory.get().nombre,
                fullName: UsuarioFactory.get().nombreFull,
                idProfile: UsuarioFactory.get().idProfile,
                fromCambiarMiPassword: true
            };
            UtilsService.showDialog("views/usuario_password.html?_="+new Date().getTime(), "UsuarioPasswordController", dialogData).then(function(answer) {
                $rootScope.goto('logout');
            }, function() {

            });
        }, function() {
            UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
        });
    };

    $rootScope.verMisAccesos = function () {
        UsuarioService.getUsuarioByToken().then(function(response){
            var dialogData = {
                id: response.data.data.id,
                nombreUsuario: UsuarioFactory.get().nombre,
                fullName: UsuarioFactory.get().nombreFull,
                idProfile: UsuarioFactory.get().idProfile,
                fromCambiarMiPassword: true
            };
            UtilsService.showDialog("views/usuario_accesos.html?_="+new Date().getTime(), "UsuarioAccesosController", dialogData).then(function(answer) {

            }, function() {

            });
        }, function() {
            UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
        });
    };

    $rootScope.isMenuVisible = function () {
        return $rootScope.menuVisible;
    };
    $rootScope.isHeaderVisible = function () {
        return $rootScope.headerVisible;
    };

    $rootScope.openLeftMenu = function () {
        $mdSidenav('left').toggle();
    };

    $rootScope.openMenu = function ($mdMenu, ev) {
        originatorEv = ev;
        $mdMenu.open(ev);
    };

    $rootScope.showTour = function() {
        return false;
    };

    // -- Estilo menu
    $rootScope.getIndexMenuClass = function(item) {
        var css = {
            item: "",
            contenedor: ""
        }
        if (item.activo) {
            css.item = 'item-on';
            css.contenedor = 'item-contenedor-on';
        } else {
            css.item = 'item-off';
            css.contenedor = 'item-contenedor-off';
        }
        if (item.separar) {
            css.contenedor += ' separado';
        }
        return css;
    }

    // -- Estilo global
    $rootScope.getEnvironmentClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "eds-environment";
        } else {
            return "common-environment";
        }
    };

    // -- Estilo para barra de bienvenido
    $rootScope.getWelcomeBarClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "welcome-eds";
        } else {
            return "welcome-common";
        }
    };

    // -- Estilo para barra de navegacion
    $rootScope.getNavBarClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "nav-bar-eds";
        } else {
            return "nav-bar-common";
        }
    };

    // -- Estilo para el logo
    $rootScope.logoEmpresa = "assets/public/logo-vaa.png";
    $rootScope.getLogoEmpresa = function() {
        return $rootScope.logoEmpresa;
    }
    $rootScope.setLogoEmpresa = function(logoEmpresa) {
        $rootScope.logoEmpresa = logoEmpresa;
    }
    $rootScope.resetLogoEmpresa = function() {
        $rootScope.logoEmpresa = "assets/public/logo-vaa.png";
    }

    // -- Estilo para progress bar
    $rootScope.getProgressBarClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "progress-eds";
        } else {
            return "progress-vaa";
        }
    };

    // -- Estilo para los botones
    $rootScope.getButtonClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "md-button-eds";
        } else {
            return "md-button-vaa";
        }
    };

    // -- Estilo para checkbox
    $rootScope.getCheckboxClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "eds-checkbox";
        } else {
            return "vaa-checkbox";
        }
    };

    // -- Estilo para paginacion
    $rootScope.getPaginationClass = function() {
        if (!angular.isUndefined(UsuarioFactory.get(true)) && UsuarioFactory.get(true).isAdmin) {
            return "pagination-eds";
        } else {
            return "pagination";
        }
    };

});

// -- apsUploadFile directive
app.directive('apsUploadFile', apsUploadFile);
function apsUploadFile() {
    var directive = {
        restrict: 'E',
        template:
            '<input id="fileInput" type="file" class="ng-hide" aria-label="file"> ' +
            '<md-button style="margin-left: 0 !important;" id="uploadButton" class="md-raised md-primary" ng-class="getButtonClass()" aria-label="attach_file">' +
            '    Cargar Logo ' +
            '</md-button>' +
            '<md-input-container  md-no-float>    ' +
            '   <input id="textInput" ng-model="fileName" type="text" placeholder="" ng-readonly="true" aria-label="file"/>' +
            '</md-input-container>',
        link: apsUploadFileLink
    };
    return directive;
}
function apsUploadFileLink(scope, element, attrs) {
    var input = $(element[0].querySelector('#fileInput'));
    var button = $(element[0].querySelector('#uploadButton'));
    var textInput = $(element[0].querySelector('#textInput'));

    if (input.length && button.length && textInput.length) {
        button.click(function(e) {
            input.click();
        });
        textInput.click(function(e) {
            input.click();
        });
    }

    input.on('change', function(e) {
        var files = e.target.files;
        if (files[0]) {
            scope.fileName = files[0].name;
        } else {
            scope.fileName = null;
        }
        scope.$apply();
    });
}

// -- Constants
app.constant('CONST', {
    ENVIRONMENT: {
        'EDS_ID': 1
    },
    PROFILES: [
        {profile: 'SA', value: 1},
        {profile: 'ADMINISTRADOR', value: 2},
        {profile: 'OPERADOR', value: 3}
    ],
    OPERATION_MODES: [
        {opm_code: 'DIURNO', opm_description: 'Modo de operación diurno'},
        {opm_code: 'NOCTURNO', opm_description: 'Modo de operación nocturno'}
    ],
    COMPLEMENTS: [
        { value: 1, label: "SIN ESPECIFICAR"},
        { value: 2, label: "DIURNO" },
        { value: 3, label: "NOCTURNO" }
    ]
});

// -- checkStrength Directive
app.directive('checkStrength', ['UtilsService', function(UtilsService) {
    return {
        replace: false,
        restrict: 'EACM',
        link: function (scope, iElement, iAttrs) {

            var strength = {
                colors: ['#F00', '#F90', '#FF0', '#9F0', '#0F0'],
                mesureStrength: function (p) {

                    if (p === undefined) {
                        return 0;
                    }
                    return UtilsService.evaluarPassword(p);
                    
                },
                getColor: function (s) {

                    var idx = 0;
                    if (s <= 10) { idx = 0; }
                    else if (s <= 20) { idx = 1; }
                    else if (s <= 30) { idx = 2; }
                    else if (s <= 40) { idx = 3; }
                    else { idx = 4; }

                    return { idx: idx + 1, col: this.colors[idx] };

                }
            };

            scope.$watch(iAttrs.checkStrength, function () {
                /* if (scope.password === '') {
                    iElement.css({ "display": "none"  });
                } else { */
                var c = strength.getColor(strength.mesureStrength(scope.password));
                iElement.css({ "display": "inline" });
                iElement.children('li')
                    .css({ "background": "#DDD" })
                    .slice(0, c.idx)
                    .css({ "background": c.col });
                // }
            });

        },
        template: '<li class="point"></li><li class="point"></li><li class="point"></li><li class="point"></li><li class="point"></li>'
    };
}]);

// -- ngRightClick Directive
app.directive('ngRightClick', function($parse) {
    return function(scope, element, attrs) {
        var fn = $parse(attrs.ngRightClick);
        element.bind('contextmenu', function(event) {
            scope.$apply(function() {
                event.preventDefault();
                fn(scope, {$event:event});
            });
        });
    };
});

// -- Excel Directive
app.factory('Excel',function($window){
    var uri='data:application/vnd.ms-excel;base64,',
        template='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
        base64=function(s){return $window.btoa(unescape(encodeURIComponent(s)));},
        format=function(s,c){return s.replace(/{(\w+)}/g,function(m,p){return c[p];})};
    return {
        tableToExcel:function(tableId,worksheetName){
            var table=$(tableId),
                ctx={worksheet:worksheetName,table:table.html()},
                href=uri+base64(format(template,ctx));
            return href;
        }
    };
});

// -- CSV Directive
app.factory('CSV',function($window){
    return {
        tableToCsv: function (tableId, worksheetName) {
            var table=$(tableId),
                ctx={worksheet:worksheetName,table:table.html()};

            var table = $(tableId);
            table = table[0];
            var csvString = '';
            for(var i=0; i<table.rows.length;i++){
                var rowData = table.rows[i].cells;
                for(var j=0; j<rowData.length; j++){
                    if (!rowData[j].innerHTML.includes("no-exportable")) {
                        rowData[j].innerHTML = rowData[j].innerHTML.trim();
                        rowData[j].innerHTML = rowData[j].innerHTML.replace("<span>", "");
                        rowData[j].innerHTML = rowData[j].innerHTML.replace("</span>", "");
                        csvString = csvString + rowData[j].innerHTML + ";";
                    }
                }
                csvString = csvString.substring(0,csvString.length - 1);
                csvString = csvString + "\r\n";
            }
            csvString = csvString.substring(0, csvString.length - 1);
            var a = $('<a/>', {
                style:'display:none',
                href:'data:application/octet-stream;base64,'+btoa(csvString),
                download: worksheetName + '.csv'
            }).appendTo('body')
            a[0].click()
            a.remove();
        }
    };
});

// -- IE prevent caching
app.config(function($httpProvider){
    $httpProvider.defaults.headers.common['Cache-Control'] = 'no-cache';
    $httpProvider.defaults.cache = false;
  
    if (!$httpProvider.defaults.headers.get) {
        $httpProvider.defaults.headers.get = {};
    }
    $httpProvider.defaults.headers.get['If-Modified-Since'] = '0';
});

// -- IE prevent caching
var test = "test";
if (typeof test.includes === "undefined") { 
    console.log("no existe includes");
    String.prototype.includes = function (str) {
        var returnValue = false;
      
        if (this.indexOf(str) !== -1) {
          returnValue = true;
        }
      
        return returnValue;
    }
}