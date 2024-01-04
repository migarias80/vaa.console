app.service('CheckService', function($http, UsuarioFactory) {

    return {
        checkArchivosDepartamentos: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/check/' + UsuarioFactory.get().idEmpresa + '/archivos/departamentos',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        checkArchivosPersonas: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/check/' + UsuarioFactory.get().idEmpresa + '/archivos/personas',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});