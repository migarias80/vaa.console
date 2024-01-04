app.service('DomainService', function($http, UsuarioFactory) {

    return {
        getDomain: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/domain/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.dom_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/domain/' + method,
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
                url: '/vaa/backend/domain/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});