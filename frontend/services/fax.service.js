app.service('FaxService', function($http, UsuarioFactory) {

    return {
        getFax: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/fax/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.fax_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/fax/' + method,
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
                url: '/vaa/backend/fax/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});