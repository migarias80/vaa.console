app.factory("UsuarioFactory", function($location){
    var token,
        nombre,
        nombreFull,
        nombreEmpresa,
        urlEmpresa,
        isAdmin,
        idEmpresa,
        idProfile,
        img;

    var setEmpresa = function(nombreEmpresa, idEmpresa, img, url) {
        this.nombreEmpresa = nombreEmpresa;
        this.idEmpresa = idEmpresa;
        this.img = img;
        this.urlEmpresa = url;

        var obj = this.get();
        localStorage.setItem("vaaData", JSON.stringify(obj));
    };

    var clearEmpresa = function () {
        this.nombreEmpresa = undefined;
        this.idEmpresa = undefined;

        var obj = this.get();
        localStorage.setItem("vaaData", JSON.stringify(obj));
    };

    var set = function(token, nombre, nombreFull, nombreEmpresa, urlEmpresa, isAdmin, idEmpresa, idProfile, img) {
        this.token = token;
        this.nombre = nombre;
        this.nombreFull = nombreFull;
        this.nombreEmpresa = nombreEmpresa;
        this.urlEmpresa = urlEmpresa;
        this.isAdmin = isAdmin;
        this.idEmpresa = idEmpresa;
        this.idProfile = idProfile;
        this.img = img;

        var obj = this.get();
        localStorage.setItem("vaaData", JSON.stringify(obj));
    };

    var get = function(silent) {
        if (angular.isUndefined(this.token)) {
            if (localStorage.getItem("vaaData") == null) {
                if (silent) {
                    return;
                } else {
                    $location.path("/");
                    return;
                }
            }
            var obj = JSON.parse(localStorage.getItem("vaaData"));
            this.token = obj.token;
            this.nombre = obj.nombre;
            this.nombreFull = obj.nombreFull;
            this.nombreEmpresa = obj.nombreEmpresa;
            this.urlEmpresa = obj.urlEmpresa;
            this.isAdmin = obj.isAdmin;
            this.idEmpresa = obj.idEmpresa;
            this.idProfile = obj.idProfile;
            this.img = obj.img;
        }
        return {
            token: this.token,
            nombre: this.nombre,
            nombreFull: this.nombreFull,
            nombreEmpresa: this.nombreEmpresa,
            urlEmpresa: this.urlEmpresa,
            isAdmin: this.isAdmin,
            idEmpresa: this.idEmpresa,
            idProfile: this.idProfile,
            img: this.img
        }
    };

    var destroy = function() {
        this.token = undefined;
        this.nombre = undefined;
        this.nombreFull = undefined;
        this.nombreEmpresa = undefined;
        this.urlEmpresa = undefined;
        this.isAdmin = undefined;
        this.idEmpresa = undefined;
        this.idProfile = undefined;
        this.img = undefined;
    };

    var setToken = function (token) {
        this.token = token
    };

    return {
        get: get,
        set: set,
        setEmpresa: setEmpresa,
        destroy: destroy,
        clearEmpresa: clearEmpresa,
        setToken: setToken
    };
});