app.service('EstadisticaService', function($http, UsuarioFactory) {

    return {
        getLlamadas: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/estadistica/llamadas/' + UsuarioFactory.get().idEmpresa + "/" + data.fechaDesde + "/" + data.fechaHasta,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getTroncales: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/estadistica/troncales/' + UsuarioFactory.get().idEmpresa + "/" + data.fechaDesde + "/" + data.fechaHasta,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getTroncalesMaximos: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/estadistica/troncales-maximos/' + UsuarioFactory.get().idEmpresa + "/" + data.fechaDesde + "/" + data.fechaHasta,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getDetalleTroncal: function (data) {
            return $http({
                method: 'get',
                url: '/vaa/backend/estadistica/detalle-troncal/' + UsuarioFactory.get().idEmpresa + "/" + data.fecha,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});