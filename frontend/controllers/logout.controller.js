app.controller('LogoutController', function($location, UtilsService, UsuarioFactory) {

    var urlEmpresa = UsuarioFactory.get().urlEmpresa;
    var path = "/";

    if (!UsuarioFactory.get().isAdmin && !UtilsService.isEmpty(urlEmpresa)) {
        path = '/login/' + urlEmpresa;
    }

    UtilsService.clearLocalStorage();
    UsuarioFactory.destroy();

    UtilsService.showToast({delay: 5000, text: "Gracias por utilizar nuestros servicios"});
    $location.path(path);

});