/**
 * USUARIOS
 * Controller del listado de usuarios
 */
app.controller('UsuariosController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, EmpresaService, $location, $rootScope, $filter, UsuarioService, CONST) {

    // -- Armado de entorno
    UtilsService.processingStage();
    $rootScope.mostrarEmpresas = false;
    if (UsuarioFactory.get().isAdmin) {
        UtilsService.getMenuEDS("Usuarios", false, (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)));
        $rootScope.mostrarEmpresas = true;
    } else {
        UtilsService.getMenu("Usuarios");
    }
    $rootScope.mostrarAdmUsuarios = false;
    if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value || UsuarioFactory.get().idProfile == CONST.PROFILES[1].value) {
        $rootScope.mostrarAdmUsuarios = true;
    }
    $scope.nombreEmpresa = UsuarioFactory.get().nombreEmpresa;
    $rootScope.nombreUsuario = UsuarioFactory.get().nombre;

    // -- Logo de la empresa
    if (UsuarioFactory.get().img != null) {
        $rootScope.setLogoEmpresa('assets/public/' + UsuarioFactory.get().img);
    }

    // Agregado de leyenda Para la administracion...
    if (angular.isUndefined(UsuarioFactory.get().nombreEmpresa)) {
        $scope.labelTituloUsuarios = "para la Administración General del VAA";
    } else {
        $scope.labelTituloUsuarios = "";
    }

    // -- Busqueda de registros
    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'nombreUsuario',
            direction: 'asc'
        }
    };
    function obtenerUsuarios() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        EmpresaService.loadEmpresa().then(function(response){
            if (angular.isUndefined(response.data.data)) {
                $scope.cargando = false;
                return;
            }
            var reemplazarItemSeleccionado = false;
            if ($scope.itemSeleccionado !== undefined) {
                reemplazarItemSeleccionado = true;
            }
            angular.forEach(response.data.data.usuarios, function(value, key) {
                var auxUsuario = $filter('filter')(CONST.PROFILES, {value: value.idProfile});
                if (!angular.isUndefined(auxUsuario) && auxUsuario.length > 0) {
                    auxUsuario = auxUsuario[0];
                    value.profile = auxUsuario.profile;
                } else {
                    value.profile = "N/A";
                }
                $scope.gridOptions.data.push(value);
                if (reemplazarItemSeleccionado) {
                    if (value.id == $scope.itemSeleccionado.id) {
                        $scope.viewItem(value, true, false);
                    }
                }
            });

            $scope.cargando = false;

            $scope.states = $scope.loadAll();
        }, function() {
            $scope.cargando = false;
        });
    }
    obtenerUsuarios();

    // -- Eventos de la grilla
    $scope.getNombreItemSeleccionado = function() {
        if (angular.isUndefined($scope.itemSeleccionado)) {
            return "Usuario seleccionado";
        } else {
            return $scope.itemSeleccionado.nombreUsuario;
        }
    };

    $scope.isSelected = function(item) {
        if (angular.isUndefined(item)) { return false; }
        if (item.id == $scope.idSeleccionado) {
            return true;
        }
        return false;
    };

    // -- Context menu
    $scope.menuOptions = [
        {
            text: 'Editar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.viewItem($itemScope.item, true);
                $scope.editarUsuario($itemScope.item);
            }
        },
        {
            text: 'Habilitar / Deshabilitar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                if ($itemScope.item.active == '0') {
                    $scope.habilitarUsuario($itemScope.item);    
                } else {
                    $scope.deshabilitarUsuario($itemScope.item);
                }
            }
        },
        {
            text: 'Eliminar',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.eliminarUsuario($itemScope.item);
            }
        },
        {
            text: 'Cambiar Password',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.setPassword($itemScope.item);
            }
        },
        {
            text: 'Ver Historial de Cambios',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.verHistorial($itemScope.item);
            }
        },
        {
            text: 'Ver Accesos del Usuario',
            click: function ($itemScope, $event, modelValue, text, $li) {
                $scope.verAccesos($itemScope.item);
            }
        }
    ];

    // -- Preview del registro
    $scope.idSeleccionado = undefined;
    $scope.itemSeleccionado = undefined;
    $scope.viewItem = function(item, forceView) {
        if (!angular.isUndefined(item)) {
            if ($scope.idSeleccionado == item.id && !forceView) {
                $scope.hidePreview();
            } else {
                $scope.idSeleccionado = item.id;
                $scope.itemSeleccionado = item;
                $scope.selectedTab = 1;
            }

            if (!angular.isUndefined($scope.idSeleccionado)) {
                var foundItem = $filter('filter')($scope.gridOptions.data, {id: item.id})[0];
                $scope.indexSeleccionado = $scope.gridOptions.data.indexOf(foundItem);
            }
        }
    };

    $scope.hidePreview = function() {
        $scope.idSeleccionado = undefined;
        $scope.nombreSeleccionado = "Usuario seleccionado";
        $scope.itemSeleccionado = undefined;
    };

    $scope.selectedItemChange = function(item) {
        $scope.viewItem(item);
    };

    // -- Crear usuario
    $scope.crearUsuario = function() {
        UtilsService.showDialog("views/usuario.html?_="+new Date().getTime(), "UsuarioController").then(function(answer) {
            obtenerUsuarios();
        }, function() {

        });
    };

    // -- Editar usuario
    $scope.editarUsuario = function(item) {
        if (UsuarioFactory.get().idProfile != CONST.PROFILES[0].value && UsuarioFactory.get().idProfile != CONST.PROFILES[1].value) {
            UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
            return;
        }
        if (item.idProfile == CONST.PROFILES[0].value) {
            UtilsService.showToast({delay: 5000, text: "No es posible editar al usuario SA"});
            return;
        }

        if (!angular.isUndefined(item)) {
            UsuarioService.getUsuarioByToken().then(function(response){
                var idUsuario = response.data.data.id;
                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value && idUsuario == item.id) {
                    // soy sa y quiero cambiar mis datos
                    UtilsService.showDialog("views/usuario.html?_=" + new Date().getTime(), "UsuarioController", item).then(function(answer) {
                        obtenerUsuarios();
                    }, function() {
        
                    });
                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value && idUsuario != item.id) {
                    // soy sa y quiero cambiar datos de otra persona
                    UtilsService.showDialog("views/usuario.html?_=" + new Date().getTime(), "UsuarioController", item).then(function(answer) {
                        obtenerUsuarios();
                    }, function() {
        
                    });
                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[1].value && idUsuario == item.id) {
                    // soy admin y quiero cambiar mis datos
                    UtilsService.showDialog("views/usuario.html?_=" + new Date().getTime(), "UsuarioController", item).then(function(answer) {
                        obtenerUsuarios();
                    }, function() {
        
                    });
                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[1].value && idUsuario != item.id) {
                    if (item.idProfile == CONST.PROFILES[2].value) {
                        // soy admin y quiero cambiar datos de un operador
                        UtilsService.showDialog("views/usuario.html?_=" + new Date().getTime(), "UsuarioController", item).then(function(answer) {
                            obtenerUsuarios();
                        }, function() {

                        });
                    } else {
                        UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                        return;
                    }
                } else {
                    UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                    return;
                }
            }, function() {
                UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
            });
            
        }
    };

    // -- Deshabilitar usuario
    $scope.deshabilitarUsuario = function(item) {
        if (UsuarioFactory.get().idProfile != CONST.PROFILES[0].value && UsuarioFactory.get().idProfile != CONST.PROFILES[1].value) {
            UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
            return;
        }

        if (!angular.isUndefined(item)) {
            if (item.idProfile == CONST.PROFILES[0].value) {
                UtilsService.showToast({delay: 5000, text: "No es posible deshabilitar al usuario SA"});
                return;
            }

            UsuarioService.getUsuarioByToken().then(function(response){
                var idUsuario = response.data.data.id;

                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value) {
                    // soy sa
                    UtilsService.showConfirm("Atención!", "Desea deshabilitar a " + item.display + "?").then(function() {
                        UsuarioService.deshabilitarUsuario(item.id).then(function successCallback(response) {
                            if (response.data.code == 1) {
                                UtilsService.showToast({delay: 5000, text: item.display + " fue deshabilitado correctamente"});
                                obtenerUsuarios();
                            } else {
                                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al deshabilitar a " + item.display});
                            }
                        }, function errorCallback(error) {
                            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                        });
                    });
                } else if (item.idProfile == CONST.PROFILES[2].value) {
                    // deshabilitar a un operador
                    UtilsService.showConfirm("Atención!", "Desea deshabilitar a " + item.display + "?").then(function() {
                        UsuarioService.deshabilitarUsuario(item.id).then(function successCallback(response) {
                            if (response.data.code == 1) {
                                UtilsService.showToast({delay: 5000, text: item.display + " fue deshabilitado correctamente"});
                                obtenerUsuarios();
                            } else {
                                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al deshabilitar a " + item.display});
                            }
                        }, function errorCallback(error) {
                            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                        });
                    });
                } else {
                    UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                    return;
                }
            }, function() {
                UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
            });
        }
    };

    // -- Habilitar usuario
    $scope.habilitarUsuario = function(item) {
        if (UsuarioFactory.get().idProfile != CONST.PROFILES[0].value && UsuarioFactory.get().idProfile != CONST.PROFILES[1].value) {
            UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
            return;
        }

        if (!angular.isUndefined(item)) {
            if (item.idProfile == CONST.PROFILES[0].value) {
                UtilsService.showToast({delay: 5000, text: "No es posible deshabilitar al usuario SA"});
                return;
            }

            UsuarioService.getUsuarioByToken().then(function(response){
                var idUsuario = response.data.data.id;

                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value) {
                    // soy sa
                    UsuarioService.habilitarUsuario(item.id).then(function successCallback(response) {
                        if (response.data.code == 1) {
                            UtilsService.showToast({delay: 5000, text: item.display + " fue habilitado correctamente"});
                            obtenerUsuarios();
                        } else {
                            UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al deshabilitar a " + item.display});
                        }
                    }, function errorCallback(error) {
                        UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                    });
                } else if (item.idProfile == CONST.PROFILES[2].value) {
                    // habilitar a un operador
                    UsuarioService.habilitarUsuario(item.id).then(function successCallback(response) {
                        if (response.data.code == 1) {
                            UtilsService.showToast({delay: 5000, text: item.display + " fue habilitado correctamente"});
                            obtenerUsuarios();
                        } else {
                            UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al deshabilitar a " + item.display});
                        }
                    }, function errorCallback(error) {
                        UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                    });
                } else {
                    UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                    return;
                }
            }, function() {
                UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
            });
        }
    };

    // -- Eliminar usuario
    $scope.eliminarUsuario = function(item) {
        if (UsuarioFactory.get().idProfile != CONST.PROFILES[0].value && UsuarioFactory.get().idProfile != CONST.PROFILES[1].value) {
            UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
            return;
        }

        if (!angular.isUndefined(item)) {
            if (item.idProfile == CONST.PROFILES[0].value) {
                UtilsService.showToast({delay: 5000, text: "No es posible eliminar un usuario SA"});
                return;
            }
            UsuarioService.getUsuarioByToken().then(function(response){
                var idUsuario = response.data.data.id;
                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value) {
                    // soy sa
                    UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.display + "?").then(function() {
                        UsuarioService.eliminarUsuario(item.id).then(function successCallback(response) {
                            if (response.data.code == 1) {
                                UtilsService.showToast({delay: 5000, text: item.display + " fue eliminado correctamente"});
                                obtenerUsuarios();
                            } else {
                                UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.display});
                            }
                        }, function errorCallback(error) {
                            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                        });
                    });
                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[1].value && idUsuario != item.id) {
                    if (item.idProfile == CONST.PROFILES[2].value) {
                        // soy admin y quiero eliminar a un operador
                        UtilsService.showConfirm("Atención!", "Desea eliminar a " + item.display + "?").then(function() {
                            UsuarioService.eliminarUsuario(item.id).then(function successCallback(response) {
                                if (response.data.code == 1) {
                                    UtilsService.showToast({delay: 5000, text: item.display + " fue eliminado correctamente"});
                                    obtenerUsuarios();
                                } else {
                                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al eliminar a " + item.display});
                                }
                            }, function errorCallback(error) {
                                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
                            });
                        });
                    } else {
                        UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                        return;
                    }
                } else {
                    UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                    return;
                }
            }, function() {
                UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
            });
            
        }
    };

    // -- Set password de un usuario
    $scope.setPassword = function(item) {
        if (UsuarioFactory.get().idProfile != CONST.PROFILES[0].value && UsuarioFactory.get().idProfile != CONST.PROFILES[1].value) {
            UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
            return;
        }

        if (!angular.isUndefined(item)) {
            UsuarioService.getUsuarioByToken().then(function(response){
                var idUsuario = response.data.data.id;
                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value && idUsuario == item.id) {
                    // soy sa y quiero cambiar mi password
                    var dialogData = {
                        id: response.data.data.id,
                        nombreUsuario: UsuarioFactory.get().nombre,
                        fullName: UsuarioFactory.get().nombreFull,
                        idProfile: UsuarioFactory.get().idProfile,
                        fromCambiarMiPassword: true
                    }
                    UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", dialogData).then(function(answer) {
                        $rootScope.goto('logout');
                    }, function() {

                    });

                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value && idUsuario != item.id) {
                    // soy sa y quiero cambiar el password de otra persona
                    UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", item).then(function(answer) {
                        obtenerUsuarios();
                    }, function() {

                    });

                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[1].value && idUsuario == item.id) {
                    // soy admin y quiero cambiar mi password
                    var dialogData = {
                        id: response.data.data.id,
                        nombreUsuario: UsuarioFactory.get().nombre,
                        fullName: UsuarioFactory.get().nombreFull,
                        idProfile: UsuarioFactory.get().idProfile,
                        fromCambiarMiPassword: true
                    }
                    UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", dialogData).then(function(answer) {
                        $rootScope.goto('logout');
                    }, function() {

                    });

                } else if (UsuarioFactory.get().idProfile == CONST.PROFILES[1].value && idUsuario != item.id) {
                    if (item.idProfile == CONST.PROFILES[2].value) {
                        // soy admin y quiero cambiar el password de un operador
                        UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", item).then(function(answer) {
                            obtenerUsuarios();
                        }, function() {
    
                        });
                    } else {
                        UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                        return;
                    }
                } else {
                    UtilsService.showToast({delay: 5000, text: "No posee permisos para realizar la acción solicitada"});
                    return;
                }
            }, function() {
                UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
            });
            

            /* if (item.idProfile == CONST.PROFILES[0].value) {
                if (UsuarioFactory.get().idProfile == CONST.PROFILES[0].value) {
                    UsuarioService.getUsuarioByToken().then(function(response){
                        if (response.data.data.idEmpresa == CONST.ENVIRONMENT.EDS_ID) {
                            UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", item).then(function(answer) {
                                obtenerUsuarios();
                            }, function() {

                            });
                        } else {
                            var dialogData = {
                                id: response.data.data.id,
                                nombreUsuario: UsuarioFactory.get().nombre,
                                fullName: UsuarioFactory.get().nombreFull,
                                idProfile: UsuarioFactory.get().idProfile,
                                fromCambiarMiPassword: true
                            }
                            UtilsService.showDialog("views/usuario_password.html?_=" + new Date().getTime(), "UsuarioPasswordController", dialogData).then(function(answer) {
                                $rootScope.goto('logout');
                            }, function() {

                            });
                        }
                    }, function() {
                        UtilsService.showToast({delay: 5000, text: "Se produjo un error al obtener los datos del usuario"});
                    });
                } else {
                    UtilsService.showToast({delay: 5000, text: "No es posible cambiar la conraseña del usuario SA"});
                }
            } else {
                UtilsService.showDialog("views/usuario_password.html?_="+new Date().getTime(), "UsuarioPasswordController", item).then(function(answer) {
                    obtenerUsuarios();
                }, function() {

                });
            } */
        }
    };

    // -- Ver historial
    $scope.verHistorial = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/usuario_historial.html?_="+new Date().getTime(), "UsuarioHistorialController", item).then(function(answer) {

            }, function() {

            });
        }
    };

    // -- Ver accesos
    $scope.verAccesos = function(item) {
        if (!angular.isUndefined(item)) {
            UtilsService.showDialog("views/usuario_accesos.html?_="+new Date().getTime(), "UsuarioAccesosController", item).then(function(answer) {

            }, function() {

            });
        }
    };

    // -- Busqueda dinamica
    $scope.loadAll = function () {
        allStates = [];
        for (var i=0; i<$scope.gridOptions.data.length; i++){
            allStates.push($scope.gridOptions.data[i]);
            allStates[i].value = $scope.gridOptions.data[i].fullName + " - " + $scope.gridOptions.data[i].nombreUsuario;
            allStates[i].display = $scope.gridOptions.data[i].fullName;
        }
        return allStates;
    };
    $scope.querySearch = function(query) {
        var result = $filter('filter')($scope.states, {value: query});
        return result;
    }

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});


/**
 * USUARIOS - DIALOG
 * Controller del dialog del usuario seleccionado
 */
app.controller('UsuarioController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, UsuarioService, dialogData, CONST, UtilsService) {

    $scope.titulo = "Nuevo Usuario";

    $scope.cancelar = function() {
        $mdDialog.cancel();
    }

    $scope.profiles = [CONST.PROFILES[1], CONST.PROFILES[2]];

    // -- Validacion de edicion del perfil
    $scope.mostrarCargaPerfil = true;
    /* if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromVerMisDatos) && dialogData.fromVerMisDatos) {
        $scope.mostrarCargaPerfil = false;
    } else {
        if (UsuarioFactory.get().isAdmin && UsuarioFactory.get().idEmpresa == CONST.ENVIRONMENT.EDS_ID) {
            $scope.mostrarCargaPerfil = false;

        } else if (!angular.isUndefined(dialogData) && dialogData.idProfile == CONST.PROFILES[0].value) {
            $scope.mostrarCargaPerfil = false;
        }
    } */

    // -- Validacion de edicion de nombre
    $scope.puedeEditarNombre = true;
    if (!angular.isUndefined(dialogData)) {
        $scope.puedeEditarNombre = false;
    }

    // -- Validacion de carga de password
    $scope.debeIndicarPassword = true;
    if (!angular.isUndefined(dialogData)) {
        $scope.debeIndicarPassword = false;
    }

    // -- Validacion de edicion del perfil
    $scope.mostrarCargaPerfil = true;
    if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromVerMisDatos)) {
        $scope.mostrarCargaPerfil = false;
    }

    $scope.verPassword = function() {
        $scope.passwordVisible = !$scope.passwordVisible;
        var tag = document.getElementById('password');

        if ($scope.passwordVisible){
            tag.setAttribute('type', 'text');
        } else {
            tag.setAttribute('type', 'password');
        }
    };

    $scope.autogenerarPassword = function() {
        $scope.password = UtilsService.generarPassword();
    };

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.nombreUsuario)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el nombre de usuario"});
            return;
        }
        if (UtilsService.isEmpty($scope.fullName)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar el nombre completo"});
            return;
        }
        if (UtilsService.isEmpty($scope.idProfile) && $scope.mostrarCargaPerfil) {
            UtilsService.showToast({delay: 5000, text: "Debe indicar el perfil del usuario"});
            return;
        }
        if (UtilsService.isEmpty($scope.password) && $scope.debeIndicarPassword) {
            UtilsService.showToast({delay: 5000, text: "Debe indicar la contraseña del usuario"});
            return;
        }

        var data = {
            id: $scope.id,
            nombreUsuario: $scope.nombreUsuario,
            fullName: $scope.fullName,
            idProfile: $scope.idProfile
        }
        if ($scope.debeIndicarPassword) {
            var shaObj = new jsSHA("SHA-256", "TEXT");
            shaObj.update($scope.password);
            data.password = shaObj.getHash("HEX");
        }
        if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromVerMisDatos) && dialogData.fromVerMisDatos) {
            UsuarioService.modificarMisDatos(data).then(function successCallback(response) {
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "Tus datos fueron guardados correctamente"});
                    $mdDialog.hide("OK");
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar tus datos"});
                }
                $scope.cargando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            });
        } else {
            UsuarioService.guardar(data).then(function successCallback(response) {
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "Usuario guardado correctamente"});
                    $mdDialog.hide("OK");
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al guardar el usuario"});
                }
                $scope.cargando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            });
        }
    };

    if (!angular.isUndefined(dialogData)) {
        $scope.titulo = "Editar Usuario";
        $scope.id = dialogData.id;
        $scope.nombreUsuario = dialogData.nombreUsuario;
        $scope.fullName = dialogData.fullName;
        $scope.idProfile = dialogData.idProfile;
    }

});


/**
 * PASSWORD USUARIO - DIALOG
 * Controller del dialog del password del usuario
 */
app.controller('UsuarioPasswordController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, UsuarioService, dialogData, CONST) {

    $scope.titulo = "Cambiar Password";

    $scope.password = '';

    $scope.cancelar = function () {
        $mdDialog.cancel();
    };

    $scope.autogenerarPassword = function() {
        $scope.password = UtilsService.generarPassword();
        if ($scope.mostrarConfirmacionPassword) {
            $scope.re_password = $scope.password;
        }
    };

    $scope.passwordVisible = false;
    $scope.verPassword = function() {
        $scope.passwordVisible = !$scope.passwordVisible;
        var tag = document.getElementById('password');
        var tag2 = document.getElementById('rePassword');

        if ($scope.passwordVisible){
            tag.setAttribute('type', 'text');
            tag2.setAttribute('type', 'text');
        } else {
            tag.setAttribute('type', 'password');
            tag2.setAttribute('type', 'password');
        }
    };

    $scope.mostrarConfirmacionPassword = false;
    // Visualizacion de "Cambiar mi password" por medio del menu debe confirmar contraseña
    if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromCambiarMiPassword) && dialogData.fromCambiarMiPassword) {
        $scope.mostrarConfirmacionPassword = true;
    }

    $scope.aceptar = function() {
        if (UtilsService.isEmpty($scope.password)) {
            UtilsService.showToast({delay: 5000, text: "Debe ingresar la nueva contraseña del usuario"});
            return;
        }
        if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromCambiarMiPassword) && dialogData.fromCambiarMiPassword) {
            if (UtilsService.isEmpty($scope.old_password)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar la contraseña actual"});
                return;
            }
            if (UtilsService.isEmpty($scope.re_password)) {
                UtilsService.showToast({delay: 5000, text: "Debe ingresar la confirmación de su contraseña"});
                return;
            }
            if ($scope.password != $scope.re_password) {
                UtilsService.showToast({delay: 5000, text: "La contraseña y su confirmación no coinciden"});
                return;
            }
        }

        var data = {
            id: dialogData.id,
            password: $scope.password,
            old_password: $scope.old_password
        };
        if (!angular.isUndefined(dialogData) && !angular.isUndefined(dialogData.fromCambiarMiPassword) && dialogData.fromCambiarMiPassword) {
            UsuarioService.updateMyPassword(data).then(function successCallback(response) {
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "La contraseña fue cambiada correctamente"});
                    $mdDialog.hide("OK");
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cambiar la contraseña del usuario"});
                }
                $scope.cargando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            });
        } else if (UsuarioFactory.get().isAdmin) {
            UsuarioService.setSAAdminPassword(data).then(function successCallback(response) {
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "La contraseña fue cambiada correctamente"});
                    $mdDialog.hide("OK");
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cambiar la contraseña del usuario"});
                }
                $scope.cargando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            });
        } else {
            UsuarioService.setPassword(data).then(function successCallback(response) {
                if (response.data.code == 1) {
                    UtilsService.showToast({delay: 5000, text: "La contraseña fue cambiada correctamente"});
                    $mdDialog.hide("OK");
                } else {
                    UtilsService.showToast({delay: 5000, text: "Ocurrió un problema al cambiar la contraseña del usuario"});
                }
                $scope.cargando = false;
            }, function errorCallback(error) {
                UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
            });
        }
    };

});


/**
 * HISTORIAL DE CAMBIOS DEL USUARIO - DIALOG
 * Controller del dialog del historial del usuario
 */
app.controller('UsuarioHistorialController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, UsuarioService, dialogData, CONST, $filter, $rootScope) {

    $scope.titulo = "Historial de Cambios del Usuario: " + dialogData.nombreUsuario;
    $scope.usuarioAU = [];
    $scope.cargando = true;

    $scope.cancelar = function () {
        $mdDialog.cancel();
    };

    $scope.aceptar = function () {
        $mdDialog.cancel();
    };

    // -- Busqueda de registros
    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'lastEditValue',
            direction: 'desc'
        }
    };
    function obtenerUsuariosAU() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        UsuarioService.getHistorial(dialogData.id).then(function successCallback(response) {
            if (angular.isUndefined(response.data.data)) {
                $scope.cargando = false;
                return;
            }
            angular.forEach(response.data.data, function(value, key) {
                var auxUsuario = $filter('filter')(CONST.PROFILES, {value: value.idProfile});
                if (!angular.isUndefined(auxUsuario) && auxUsuario.length > 0) {
                    auxUsuario = auxUsuario[0];
                    value.profile = auxUsuario.profile;
                } else {
                    value.profile = "N/A";
                }
                value.lastEditValue = new Date(value.lastEdit);
                value.lastEdit = $filter('date')(new Date(value.lastEdit), 'dd/MM/yyyy HH:mm:ss');
                $scope.gridOptions.data.push(value);
            });
            $scope.cargando = false;
        }, function(error) {
            $scope.cargando = false;
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }
    obtenerUsuariosAU();

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});


/**
 * HISTORIAL DE ACCESOS DEL USUARIO - DIALOG
 * Controller del dialog del historial del usuario
 */
app.controller('UsuarioAccesosController', function($scope, $http, UsuarioFactory, UtilsService, $mdDialog, UsuarioService, dialogData, $rootScope, $filter) {

    $scope.titulo = "Historial de Accesos del Usuario: " + dialogData.nombreUsuario;
    $scope.usuarioAU = [];
    $scope.cargando = true;

    $scope.cancelar = function () {
        $mdDialog.cancel();
    };

    $scope.aceptar = function () {
        $mdDialog.cancel();
    };

    // -- Busqueda de registros
    $scope.gridOptions = {
        data: [],
        urlSync: false,
        sort: {
            predicate: 'last_access_value',
            direction: 'desc'
        }
    };
    function obtenerAccesosDelUsuario() {
        $scope.cargando = true;
        $scope.gridOptions.data = [];
        UsuarioService.getAllAccess(dialogData.id).then(function successCallback(response) {
            if (angular.isUndefined(response.data.data)) {
                $scope.cargando = false;
                return;
            }
            angular.forEach(response.data.data, function(value, key) {
                value.last_access_value = new Date(value.last_access);
                $scope.gridOptions.data.push(value);
            });
            $scope.cargando = false;
        }, function(error) {
            $scope.cargando = false;
            UtilsService.showToast({delay: 5000, text: UtilsService.processError(error)});
        });
    }
    obtenerAccesosDelUsuario();

    // -- Redefinir en $scope la funcion del $rootScope para el paginador
    $scope.getPaginationClass = $rootScope.getPaginationClass;

});