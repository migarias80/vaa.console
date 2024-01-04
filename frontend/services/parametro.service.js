app.service('ParametroService', function($http, UsuarioFactory, $q, 
    QueryValidationCache, 
    OpcionesDeTransferenciaCache,
    OpcionesDeTransferenciaDepA1Cache, 
    OpcionesDeTransferenciaDepA2Cache, 
    OpcionesDeTransferenciaDepA3Cache, 
    OpcionesDeTransferenciaPersonaA1Cache, 
    OpcionesDeTransferenciaPersonaA2Cache, 
    OpcionesDeTransferenciaPersonaA3Cache, 
    OpcionesDeConfirmacionCache,
    OpcionesDeGramaticaCache,
    DatosTelefonicosCache,
    DayTypeCache,
    OperationModesCache) {

    return {
        getOpcionesDeTransferencia: function () {
            var deferred = $q.defer();
            var getData = true;
            if (OpcionesDeTransferenciaCache.getData() != null) {
                deferred.resolve(OpcionesDeTransferenciaCache.getData());
                getData = false;
            }
            if (getData) {
                $http({
                    method : 'get',
                    url: '/vaa/backend/parametros/trans',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    OpcionesDeTransferenciaCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getOpcionesDeConfirmacion: function () {
            var deferred = $q.defer();
            var getData = true;
            if (OpcionesDeConfirmacionCache.getData() != null) {
                deferred.resolve(OpcionesDeConfirmacionCache.getData());
                getData = false;
            }
            if (getData) {
                $http({
                    method : 'get',
                    url: '/vaa/backend/parametros/confirm',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    OpcionesDeConfirmacionCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getOpcionesDeGramatica: function () {
            var deferred = $q.defer();
            var getData = true;
            if (OpcionesDeGramaticaCache.getData() != null) {
                deferred.resolve(OpcionesDeGramaticaCache.getData());
                getData = false;
            }
            if (getData) {
                $http({
                    method : 'get',
                    url: '/vaa/backend/parametros/grammar-opt',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    OpcionesDeGramaticaCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getDatosTelefonicosPredefinidos: function () {
            var deferred = $q.defer();
            var getData = true;
            if (DatosTelefonicosCache.getData() != null) {
                deferred.resolve(DatosTelefonicosCache.getData());
                getData = false;
            }
            if (getData) {
                var data = { parametros: ['FAX_DAY', 'FAX_NIGHT', 'VOICE_MAIL_DAY', 'VOICE_MAIL_NIGHT'] };
                $http({
                    method: 'post',
                    url: '/vaa/backend/parametros/get/' + UsuarioFactory.get().idEmpresa,
                    data: data,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    DatosTelefonicosCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        clearDatosTelefonicosPredefinidos: function() {
            DatosTelefonicosCache.setData(undefined);
        },
        getDayTypes: function () {
            var deferred = $q.defer();
            var getData = true;
            if (DayTypeCache.getData() != null) {
                deferred.resolve(DayTypeCache.getData());
                getData = false;
            }
            if (getData) {
                $http({
                    method: 'get',
                    url: '/vaa/backend/parametros/day-types',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    DayTypeCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getOperationModes: function () {
            var deferred = $q.defer();
            var getData = true;
            if (OperationModesCache.getData() != null) {
                deferred.resolve(OperationModesCache.getData());
                getData = false;
            }
            if (getData) {
                $http({
                    method: 'get',
                    url: '/vaa/backend/parametros/operation-modes',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    OperationModesCache.setData(response);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getBandasHorarias: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/parametros/bandas-horarias/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getParametros: function (data) {
            if (typeof data === 'undefined') {
                return $http({
                    method: 'get',
                    url: '/vaa/backend/parametros/get/' + UsuarioFactory.get().idEmpresa,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                });
            } else {
                return $http({
                    method: 'post',
                    url: '/vaa/backend/parametros/get/' + UsuarioFactory.get().idEmpresa,
                    data: data,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                });
            }
        },
        getQueryValidation: function (parName) {
            var deferred = $q.defer();
            var getData = true;
            if (QueryValidationCache.getData() != null) {
                var data = QueryValidationCache.getData();
                if (!angular.isUndefined(data[parName])) {
                    getData = false;
                    deferred.resolve(data[parName]);
                }
            }
            if (getData) {
                $http({
                    method : 'get',
                    url : '/vaa/backend/parametros/query-validation/' + parName,
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': UsuarioFactory.get().token
                    }
                }).then(function(response) {
                    var data = [];
                    data[parName] = {};
                    data[parName].data = response.data.data;
                    var response = {
                        data: {
                            data: data[parName].data
                        }
                    };
                    QueryValidationCache.pushData(response, parName);
                    deferred.resolve(response);
                });
            }
            return deferred.promise;
        },
        getQueryValidationByQueryId: function (queryId, empresaId) {
            return $http({
                method : 'get',
                url : '/vaa/backend/parametros/query-validation-queryid/' + queryId + "/" + empresaId,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        // TODO: Obsoleto
        /* guardarBandaHoraria: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.ban_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/bandas-horarias/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }, */
        guardarBandasHorarias: function (data) {
            for (var i in data) {
                data[i].COMPANY_ID = UsuarioFactory.get().idEmpresa;
            }
            var method = "crear";
            if (!angular.isUndefined(data.ban_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/bandas-horarias/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminarBandaHoraria: function (dayType, opmCode, start, end) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/bandas-horarias/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + dayType + "/" + opmCode + "/" + start + "/" + end,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminarBandaHorariaPorDia: function (dayType, opmCode, start, end) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/bandas-horarias/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + dayType,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        modificarParametros: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/modificar',
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getFeriados: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/parametros/feriados/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardarFeriado: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.hol_date_old)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/feriados/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminarFeriado: function (id) {
            var method = "";
            return $http({
                method: 'post',
                url: '/vaa/backend/parametros/feriados/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getModoDeOperacionDeLaConsola: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/parametros/modo-operacion-consola',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});

app.factory('QueryValidationCache', function(){
    var data = [];

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }
    var pushData = function(push, parName){
        if (data == null) {
            data = [];
        }
        data[parName] = push;
    }

    return {
        setData: setData,
        getData: getData,
        pushData: pushData
    };
});

app.factory('OpcionesDeTransferenciaCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaDepA1Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaDepA2Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaDepA3Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaPersonaA1Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaPersonaA2Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeTransferenciaPersonaA3Cache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeConfirmacionCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OpcionesDeGramaticaCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('DatosTelefonicosCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('DayTypeCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});

app.factory('OperationModesCache', function(){
    var data = null;

    var setData = function(newObj) {
        data = newObj;
    }
    var getData = function(){
        return data;
    }

    return {
        setData: setData,
        getData: getData
    };
});
