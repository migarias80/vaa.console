app.service('VoiceMailService', function($http, UsuarioFactory) {

    return {
        getVoiceMail: function () {
            return $http({
                method: 'get',
                url: '/vaa/backend/voicemail/' + UsuarioFactory.get().idEmpresa,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        },
        guardar: function (data) {
            data.COMPANY_ID = UsuarioFactory.get().idEmpresa;
            var method = "crear";
            if (!angular.isUndefined(data.vma_id)) {
                method = "modificar";
            }
            return $http({
                method: 'post',
                url: '/vaa/backend/voicemail/' + method,
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
                url: '/vaa/backend/voicemail/eliminar/' + UsuarioFactory.get().idEmpresa + "/" + id,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': UsuarioFactory.get().token
                }
            });
        }
    }

});