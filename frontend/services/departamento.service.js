app.service('DepartamentoService', function($http, UsuarioFactory) {

    return {
        getDepartamento: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/department/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getDepartamentoMin: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/department/' + UsuarioFactory.get().idEmpresa + '/min',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        loadDepartamento: function (id) {
            return $http({
                method: 'get',
                url: '/vaa/backend/department/' + UsuarioFactory.get().idEmpresa + '/' + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.dep_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/department/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminar: function (id) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/department/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getCantidadDeDepartamentos: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/department/' + UsuarioFactory.get().idEmpresa + '/get/total',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});