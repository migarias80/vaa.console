app.service('PersonaService', function($http, UsuarioFactory) {

    return {
        getPersona: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getPersonaMin: function (filter) {
            return $http({
                method: 'post',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/min',
                data: filter,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getPersonaMinNoCast: function (filter) {
            return $http({
                method: 'post',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/min/no-cast',
                data: filter,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.phb_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/persona/' + method,
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
                url: '/vaa/backend/persona/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getSecretaria: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/get/secretaria',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getSecretariasYTrasnferibles: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/get/secretaria-transferible',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        loadPersona: function (id) {
            return $http({
                method: 'get',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/' + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getCantidadDePersonas: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/persona/' + UsuarioFactory.get().idEmpresa + '/get/total',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});