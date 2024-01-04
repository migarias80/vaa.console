/**
 * LOGIN
 * Controller del login del sistema
 */
app.controller('LoginController', function($scope, $http, LoginService, UtilsService, $location, EmpresaService, UsuarioFactory, UsuarioService) {

    UtilsService.processingStage();

    if ($location.path().split("/").length > 2 && ($location.path().split("/"))[1] == 'login') {
        $scope.urlEmpresa = ($location.path().split("/"))[2];
    }
    if (!angular.isUndefined($scope.urlEmpresa)) {
        EmpresaService.getByURL($scope.urlEmpresa).then(function successCallback(response) {
            if (!angular.isUndefined(response.data.data)) {
                if (response.data.data.vaa_active == 1) {
                    $scope.nombreEmpresa = response.data.data.name;
                    $scope.imgEmpresa = response.data.data.img;
                    if ($scope.imgEmpresa != null) {
                        $scope.logoEmpresaLogin = "assets/public/" + $scope.imgEmpresa;
                    } else {
                        $scope.logoEmpresaLogin = "assets/public/logo-vaa.png";
                    }
                } else {
                    UtilsService.showToast({delay: 5000, text: "La URL utilizada corresponde a una empresa que está deshabilitada"});
                    $scope.logoEmpresaLogin = "assets/public/logo-vaa.png";
                }
            } else {
                UtilsService.showToast({delay: 5000, text: "La URL utilizada no pertenece a ninguna empresa"});
                $scope.logoEmpresaLogin = "assets/public/logo-vaa.png";
            }
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            $scope.logoEmpresaLogin = "assets/public/logo-vaa.png";
        });
    } else {
        $scope.logoEmpresaLogin = "assets/public/logo-vaa.png";
    }

    // -- Login
    $scope.cargando = false;
    $scope.login = function () {
        if ($scope.cargando) return;
        if (UtilsService.isEmpty($scope.nombre)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar un nombre"});
            return;
        }
        if (UtilsService.isEmpty($scope.password)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar una contraseña"});
            return;
        }
        $scope.cargando = true;

        if (!angular.isUndefined($scope.urlEmpresa)) {
            EmpresaService.getByURL($scope.urlEmpresa).then(function successCallback(responseEmpresa) {
                $scope.cargando = false;
                if (responseEmpresa.data.data !== undefined && responseEmpresa.data.data.vaa_active == 1) {
                    processLogin();
                } else if (responseEmpresa.data.data !== undefined && responseEmpresa.data.data.vaa_active == 0) {
                    $scope.nombre = "";
                    $scope.password = "";
                    UtilsService.showToast({delay: 5000, text: "La URL utilizada corresponde a una empresa que está deshabilitada"});
                } else {
                    UtilsService.showToast({delay: 5000, text: "La URL utilizada no pertenece a ninguna empresa"});
                    $scope.nombre = "";
                    $scope.password = "";
                }
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                $scope.cargando = false;
            });
        } else {
            processLogin();
        }
    };
    
    function processLogin() {
        LoginService.login($scope.nombre, $scope.password, $scope.urlEmpresa).then(function(response) {
            if (response.data.data == 1) {
                UsuarioFactory.set(response.headers().authorization, $scope.nombre);
                LoginService.getDecodeToken().then(function successCallback(response2) {
                    UsuarioFactory.set(
                        response.headers().authorization,
                        $scope.nombre,
                        response2.data.data.ufullname,
                        $scope.nombreEmpresa,
                        $scope.urlEmpresa,
                        response2.data.data.bid == 1,
                        response2.data.data.bid,
                        response2.data.data.pid,
                        $scope.imgEmpresa
                    );

                    if (UsuarioFactory.get().isAdmin) {
                        $scope.cargando = false;
                        UtilsService.showToast({delay: 5000, text: "Bienvenido " + $scope.nombre + " @ " + "Administración de Empresas del VAA"});
                        $location.path('/home-eds');
                    } else {
                        $scope.cargando = false;
                        UtilsService.showToast({delay: 5000, text: "Bienvenido " + $scope.nombre + " @ " + $scope.nombreEmpresa});
                        $location.path('/home');
                    }
                });
            } else {
                $scope.cargando = false;
                if (response.data.message !== undefined) {
                    UtilsService.showToast({delay: 5000, text: response.data.message});
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un error inesperado al ingresar al sistema. Verifique archivos de configuración."});
                }
                $scope.password = "";
            }
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            $scope.cargando = false;
        });
    }

    $scope.getLogoEmpresaLogin = function() {
        return "assets/public/logo-vaa.png";
    }

    // -- Estilo para boton login
    $scope.getLoginButtonClass = function() {
        if (!angular.isUndefined($scope.urlEmpresa)) {
            return "vaa-md-raised-verde";
        } else {
            return "vaa-md-raised-eds";
        }
    };

    // -- Estilo para progress bar
    $scope.getProgressBarClassLogin = function() {
        if (!angular.isUndefined($scope.urlEmpresa)) {
            return "progress-vaa";
        } else {
            return "progress-eds";
        }
    };


});