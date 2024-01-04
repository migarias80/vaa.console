app.controller('SessionExpiredController', function($location, UtilsService, UsuarioFactory) {

    var urlEmpresa = UsuarioFactory.get().urlEmpresa,
        path = "/";
    if (UtilsService.isEmpty(urlEmpresa)) {
        path = '/';
    } else {
        path = '/login/' + urlEmpresa;
    }

    UtilsService.clearLocalStorage();
    UsuarioFactory.destroy();

    UtilsService.showToast({delay: 5000, text: "Su sesión expiró, para continuar ingrese nuevamente"});
    $location.path(path);

});