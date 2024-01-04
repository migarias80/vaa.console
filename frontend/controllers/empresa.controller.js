/**
 * EMPRESA
 * Controller del dialog de la empresa seleccionada
 */
app.controller('EmpresaController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, EmpresaService, dialogData) {

    $scope.titulo = "Nueva empresa";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    };

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.nombre)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el nombre de la empresa"});
            return;
        }
        if (UtilsService.isEmpty($scope.url_name)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la URL de la empresa"});
            return;
        }
        if (UtilsService.isEmpty($scope.password) && $scope.editarPwd) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la contraseña del usuario SA de la empresa"});
            return;
        }
        if (!isURLValid($scope.url_name)) {
            UtilsService.showToast({delay: 5000, text: "La URL de la empresa es incorrecta"});
            return;
        }

        var data = {
            name: $scope.nombre,
            url_name: $scope.url_name,
            id: $scope.id,
            img: $scope.img,
            dnis_regex: "",
            output_route: "",
            dnis_regex_ext: "",
            tts_mode: "A"
        };
        if ($scope.editarPwd) {
            data.password = $scope.password;
        }
        EmpresaService.guardar(data).then(function successCallback(response) {
            if (response.data.data == 1) {
                UtilsService.showToast({delay: 5000, text: "Empresa guardada correctamente"});
                $mdDialog.hide("OK");
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar la empresa"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    };

    function isURLValid(str) {
        return /^[0-9a-zA-Z_-]+$/.test(str);
    }

    $scope.autogenerarPassword = function() {
        $scope.password = UtilsService.generarPassword();
    };

    $scope.nombre = "";
    $scope.armarUrl = function() {
        if (angular.isUndefined($scope.nombre)) {
            $scope.url_name = ""
        } else if ($scope.nombre.length <= 10) {
            $scope.url_name = $scope.nombre.split(" ").join("-");
        }
    };

    $scope.editarPwd = true;
    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Editar empresa";
        $scope.nombre = dialogData.name;
        $scope.url_name = dialogData.url_name;
        $scope.id = dialogData.id;
        $scope.img = dialogData.img;
        $scope.editarPwd = false;
    }
    $scope.password = "";
    
    $scope.verPassword = function() {
        $scope.passwordVisible = !$scope.passwordVisible;
        var tag = document.getElementById('password');

        if ($scope.passwordVisible){
            tag.setAttribute('type', 'text');
        } else {
            tag.setAttribute('type', 'password');
        }
    };

});