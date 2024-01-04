app.service('EmpresaService', function($http, UsuarioFactory, UtilsService) {

    return {
        getByURL: function (url) {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/url/' + url,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            });
        },
        getEmpresa: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        loadEmpresa: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/get/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            if (!angular.isUndefined(data.password)) {
                data.password = UtilsService.createHash(data.password);
            }
            var method = "crear";
            if (!angular.isUndefined(data.id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/' + method,
                data: data,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        eliminar: function (id) {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/eliminar/' + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        uploadImage: function (data, callback) {
            return $.ajax({
                url: '/vaa/backend/utils/UploadImage.php',
                dataType: 'script',
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: 'post',
                success: callback
            });
        },
        setImage: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-image',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getEvaluacion: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/evaluacion/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setRegex: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-regex',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setRegex_Ext: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-regex-ext',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setOutputRoute: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-output-route',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        evaluarDNIS: function (dnis) {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/evaluacion/' + UsuarioFactory.get().idEmpresa + '/dnis/' + dnis,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setCantMaxPersonas: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-cant-max-personas',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setCantMaxDepartamentos: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-cant-max-departamentos',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setCantMaxLineas: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-cant-max-lineas',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setContacto: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-contacto',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setNotas: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-notas',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setEnabled: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-enabled',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setDisabled: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-disabled',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setConfiguracionGeneral: function (data) {
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-configuracion-general',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        setTTSMode: function (data) {
            data.id = UsuarioFactory.get().idEmpresa;
            return $http({
                method: 'post',
                url: '/vaa/backend/empresa/set-tts-mode',
                data: data,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        getRutasMHCDeEmpresa: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/empresa/mhc/rutas/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});