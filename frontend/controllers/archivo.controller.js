/**
 * ARCHIVOS
 * Controller del listado de archivos
 */
app.controller('ArchivosController', function($scope, $http, UsuarioFactory, UtilsService, $rootScope, CheckService, $filter, CONST, CSV) {

    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Archivos", false);
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Archivos");
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

    // -- Mostrar u ocultar cada tabla
    $scope.mostrarDepartamentos = false;
    $scope.mostrarPersonas = false;
    $scope.cargandoDepartamentos = true;
    $scope.cargandoPersonas = true;
    $scope.departamentosOk = false;
    $scope.departamentosError = false;
    $scope.personasOk = false;
    $scope.personasError = false;
    $scope.personasWarning = false;

    $scope.gridOptionsDepartamentos = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'archivo',
            direction: 'asc'
        }
    };
    $scope.gridOptionsPersonas = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'archivo',
            direction: 'asc'
        }
    };
    function realizarCheckDeArchivos() {
        $scope.cargandoDepartamentos = true;
        $scope.cargandoPersonas = true;
        $scope.departamentosOk = false;
        $scope.departamentosError = false;
        $scope.personasOk = false;
        $scope.personasError = false;
        $scope.personasWarning = false;

        $scope.gridOptionsDepartamentos.data = [];
        CheckService.checkArchivosDepartamentos().then(function(response){
            angular.forEach(response.data.data, function(value, key) {
                $scope.gridOptionsDepartamentos.data.push(value);
            });
            if (response.data.data === undefined) {
                $scope.departamentosOk = true;
                $scope.departamentosError = false;
            } else if (response.data.data.length == 0) {
                $scope.departamentosOk = true;
                $scope.departamentosError = false;
            } else {
                $scope.departamentosOk = false;
                $scope.departamentosError = true;
                $scope.mostrarDepartamentos = true;
            }
            $scope.cargandoDepartamentos = false;
        }, function() {
            $scope.cargandoDepartamentos = false;
        });

        $scope.gridOptionsPersonas.data = [];
        CheckService.checkArchivosPersonas().then(function(response){
            var data = [];
            angular.forEach(response.data.data, function(value, key) {
                if (value.archivo.toUpperCase().indexOf('_SECRETARY') > 0) {
                    value.obligatorioOpcional = "Opcional";
                } else {
                    value.obligatorioOpcional = "Obligatorio";
                }
                data.push(value);
            });
            $scope.gridOptionsPersonas.data = data;
            if (response.data.data === undefined) {
                $scope.personasOk = true;
                $scope.personasError = false;
                $scope.personasWarning = false;
            } else if (response.data.data.length == 0) {
                $scope.personasOk = true;
                $scope.personasError = false;
                $scope.personasWarning = false;
            } else {
                $scope.personasOk = false;
                $scope.mostrarPersonas = true;
            
                for (var i in response.data.data) {
                    if (response.data.data[i].tipo == 3) {
                        $scope.personasWarning = true;
                    } else if (response.data.data[i].tipo == 2) {
                        $scope.personasError = true;
                    }
                }
            }
            $scope.cargandoPersonas = false;
        }, function() {
            $scope.cargandoPersonas = false;
        });
    }
    realizarCheckDeArchivos();

    $scope.exportToCsv = function(tableId, fileName) {
        if (angular.isUndefined(UsuarioFactory.get().urlEmpresa)) {
            fileName += "_general";
            CSV.tableToCsv(tableId, fileName);
        } else {
            fileName += "_" + UsuarioFactory.get().urlEmpresa;
            CSV.tableToCsv(tableId, fileName);
        }
    }

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});