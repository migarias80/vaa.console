app.service('LoginService', function($http, UtilsService, UsuarioFactory) {

    return {
        login: function (nombre, password, urlEmpresa) {
            var data = {
                nombreUsuario: nombre,
                urlEmpresa: urlEmpresa
            }
            data.password = UtilsService.createHash(password);
            return $http({
                method: 'post',
                url: '/vaa/backend/usuario/login',
                data: data,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
        },
        getDecodeToken: function (token) {
            return $http({
                method: 'get',
                url: '/vaa/backend/usuario/token',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});