/**
 * DOMAIN - DIALOG
 * Controller del dialog del domain seleccionado
 */
app.controller('DomainController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, DomainService, dialogData, $rootScope) {

    $scope.titulo = "Nuevo regex";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.dom_use_ani_ip_for_refer = false;

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.dom_regex)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el Regex"});
            return;
        }
        if (UtilsService.isEmpty($scope.dom_domain)) {
            if ($scope.dom_regex == "DEFAULT") {
                $scope.dom_domain = "";
            } else {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar el dominio"});
                return;
            }
        }
        if (!$scope.expresionRegularValid) {
            UtilsService.showToast({delay: 5000, text: "La expresión regular es incorrecta"});
            return;
        }
        
        var data = {
            dom_id: $scope.dom_id,
            dom_regex: $scope.dom_regex,
            dom_domain: $scope.dom_domain,
            dom_use_ani_ip_for_refer: $scope.dom_use_ani_ip_for_refer? 1 : 0
        }
        DomainService.guardar(data).then(function successCallback(response) {
            if (response.data.code == 1) {
                UtilsService.showToast({delay: 5000, text: "Dominio guardado correctamente"});
                $mdDialog.hide("OK");
            } else {
                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar el dominio"});
            }
            $scope.cargando = false;
        }, function errorCallback(error) {
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }

    // -- Validacion de regex
    $scope.expresionRegularValid = false;
    $scope.isExpresionRegularValid = function() {
        try {
            new RegExp($scope.dom_regex);
            $scope.expresionRegularValid = true;
            return true;
        } catch(e) {
            $scope.expresionRegularValid = false;
            return false;
        }
    }

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Regex " + dialogData.dom_regex;
        $scope.dom_id = dialogData.dom_id;
        $scope.dom_regex = dialogData.dom_regex;
        $scope.dom_domain = dialogData.dom_domain;
        if (dialogData.dom_use_ani_ip_for_refer == '1') {
            $scope.dom_use_ani_ip_for_refer = true;
        } else {
            $scope.dom_use_ani_ip_for_refer = false;
        }
        $scope.isExpresionRegularValid();
    }

    // -- Redefinir en $scope la funcion del $rootScope para el checkbox
    $scope.getCheckboxClass = $rootScope.getCheckboxClass;

});