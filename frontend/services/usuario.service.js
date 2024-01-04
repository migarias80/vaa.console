app.service('UsuarioService', function($http, UsuarioFactory, UtilsService) {

    return {
        guardar: function (data) {
            data.idEmpresa = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        deshabilitarUsuario: function (id) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/deshabilitar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        habilitarUsuario: function (id) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/habilitar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminarUsuario: function (id) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        modificarMisDatos: function (data) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/modificar-mis-datos',
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getUsuarioByToken: function () {
            var method = "";
            return $http({
                method: 'get',
                url: '/vaa/backend/usuario/get',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setPassword: function (data) {
            if (!angular.isUndefined(data.password)) {
                data.password = UtilsService.createHash(data.password);
            }
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/password-set/' + UsuarioFactory.get().idEmpresa,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setSAPassword: function (data) {
            if (!angular.isUndefined(data.password)) {
                data.password = UtilsService.createHash(data.password);
            }
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/password-set-sa/' + UsuarioFactory.get().idEmpresa,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setSAAdminPassword: function (data) {
            if (!angular.isUndefined(data.password)) {
                data.password = UtilsService.createHash(data.password);
            }
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/password-set-sa-admin/' + UsuarioFactory.get().idEmpresa,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        updateMyPassword: function (data) {
            data.password = UtilsService.createHash(data.password);
            data.old_password = UtilsService.createHash(data.old_password);

            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/password/' + UsuarioFactory.get().idEmpresa,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        updateLastAccess: function (data) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/update-last-access',
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getLastAccess: function () {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/get-last-access/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getHistorial: function (id) {
            var method = "";
            return $http({
                method: 'get',
                url: '/vaa/backend/usuario/get-historial/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getAllAccess: function (id) {
            var method = "";
            return $http({
                method: 'get',
                url: '/vaa/backend/usuario/get-all-access/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});