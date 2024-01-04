app.service('UtilsService', function($mdToast, $rootScope, $location, $mdDialog, UsuarioFactory, CONST) {

    return {
        // -- Comunes
        showToast: function (data) {
            $mdToast.show(
                $mdToast.simple()
                    .textContent(data.text)
                    .position('bottom left')
                    .hideDelay(angular.isUndefined(data.delay)? 2000 : data.delay)
            );
        },
        showDialog: function(template, controller, dialogData, multiple) {
            return $mdDialog.show({
                controller: controller,
                templateUrl: template,
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                locals: {dialogData: dialogData},
                width: 100,
                multiple: multiple!==undefined? multiple : false
            });
        },
        showConfirm: function(titulo, texto) {
            var confirm = $mdDialog.confirm()
                .title(titulo)
                .textContent(texto)
                .ariaLabel('Lucky day')
                .ok('Aceptar')
                .cancel('Cancelar');

            return $mdDialog.show(confirm)
        },
        processError: function(response) {
            if (!this.isEmpty(response.data.exception)) {
                if (response.data.exception.length > 0) {
                    if (!this.isEmpty(response.data.exception[0].message)) {
                        return response.data.exception[0].message;
                    }
                }
            }
            if (!this.isEmpty(response.data.message)) {
                return response.data.message;
            }
            return "Se produjo un error inesperado, por favor intente nuevamente";
        },
        evalAccion: function(transferOption) {
            if (!angular.isUndefined(transferOption.tad_int_guide)) {
                if (transferOption.tad_int_guide && transferOption.tad_daytime) {
                    return "INT-DIUR";
                } else if (transferOption.tad_int_guide && !transferOption.tad_daytime) {
                    return "INT-NOCT";
                } else if (!transferOption.tad_int_guide && transferOption.tad_daytime) {
                    return "EXT-DIUR";
                } else if (!transferOption.tad_int_guide && !transferOption.tad_daytime) {
                    return "EXT-NOCT";
                }
            } else if (!angular.isUndefined(transferOption.tap_int_guide)) {
                if (transferOption.tap_int_guide && transferOption.tap_daytime) {
                    return "INT-DIUR";
                } else if (transferOption.tap_int_guide && !transferOption.tap_daytime) {
                    return "INT-NOCT";
                } else if (!transferOption.tap_int_guide && transferOption.tap_daytime) {
                    return "EXT-DIUR";
                } else if (!transferOption.tap_int_guide && !transferOption.tap_daytime) {
                    return "EXT-NOCT";
                }
            }
            return null;
        },
        getDestinoTransferencia: function (transfer_type) {
            if (transfer_type == "C") {
                return 1;
            } else if (transfer_type == "P") {
                return 2;
            } else if (transfer_type == "S") {
                return 3;
            } else {
                return "";
            }
        },
        getTipoDeAccion: function (accion) {
            if (!angular.isUndefined(accion.tad_int_guide)) {
                if (accion.tad_order == 0 && accion.tad_busy) {
                    return "A1";
                } else if (accion.tad_order == 1 && !accion.tad_busy) {
                    return "A2";
                } else if (accion.tad_order == 1 && accion.tad_busy) {
                    return "A2B";
                } else if (accion.tad_order == 2 && !accion.tad_busy) {
                    return "A3";
                } else if (accion.tad_order == 2 && accion.tad_busy) {
                    return "A3B";
                }
                return "";
            } else if (!angular.isUndefined(accion.tap_int_guide)) {
                if (accion.tap_order == 0 && accion.tap_busy) {
                    return "A1";
                } else if (accion.tap_order == 1 && !accion.tap_busy) {
                    return "A2";
                } else if (accion.tap_order == 1 && accion.tap_busy) {
                    return "A2B";
                } else if (accion.tap_order == 2 && !accion.tap_busy) {
                    return "A3";
                } else if (accion.tap_order == 2 && accion.tap_busy) {
                    return "A3B";
                }
                return "";
            }
        },
        zeroFill: function pad(n, width, z) {
            z = z || '0';
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        },
        generarPassword: function() {
            var password = "";
            var passwordValid = false;
            do {
                password = Array(10).fill("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz").map(function(x) { 
                    return x[Math.floor(Math.random() * x.length)] 
                });

                var caracterEspecial = Array(1).fill("*!#$@").map(function(x) { 
                    return x[Math.floor(Math.random() * x.length)] 
                }).join('');

                var posCaracterEspecial = Math.floor(Math.random() * (password.length - 1));
                password[posCaracterEspecial] = caracterEspecial;
                password = password.join('');

                if (this.evaluarPassword(password) > 40) {
                    passwordValid = true;
                }
            } while (!passwordValid);
            return password;
        },
        evaluarPassword: function(p) {
            var _force = 0;
            var _regex = /[$-/:-?{-~!"^_`\[\]]/g;
            var _lowerLetters = /[a-z]+/.test(p);
            var _upperLetters = /[A-Z]+/.test(p);
            var _numbers = /[0-9]+/.test(p);
            var _symbols = _regex.test(p);

            var _flags = [_lowerLetters, _upperLetters, _numbers, _symbols];
            var _passedMatches = $.grep(_flags, function (el) { return el === true; }).length;

            _force += 2 * p.length + ((p.length >= 10) ? 1 : 0);
            _force += _passedMatches * 10;

            // penality (short password)
            _force = (p.length <= 6) ? Math.min(_force, 10) : _force;

            // penality (poor variety of characters)
            _force = (_passedMatches == 1) ? Math.min(_force, 10) : _force;
            _force = (_passedMatches == 2) ? Math.min(_force, 20) : _force;
            _force = (_passedMatches == 3) ? Math.min(_force, 40) : _force;

            return _force;
        },
        createHash: function(password) {
            var shaObj = new jsSHA("SHA-256", "TEXT");
            shaObj.update(password);
            return shaObj.getHash("HEX");
        },
        timeStampToHHMM: function(value) {
            var auxTime = new Date(value);
            var returnTime = "";
            if (auxTime.getHours() < 10) {
                returnTime += "0" + auxTime.getHours();
            } else {
                returnTime += auxTime.getHours();
            }
            returnTime += ":";
            if (auxTime.getMinutes() < 10) {
                returnTime += "0" + auxTime.getMinutes();
            } else {
                returnTime += auxTime.getMinutes();
            }
            return returnTime;
        },
        HHMMToTimeStamp: function(value) {
            var hh = "";
            var mm = "";
            if (value.includes(":")) {
                value = value.split(":");
                hh = value[0];
                mm = value[1];
            } else {
                hh = value.substring(0, 2);
                mm = value.substring(2, 4);
            }
            return new Date(1987, 4, 12, hh, mm, 00).getTime();
        },

        // -- Charts
        generateChartLabel: function(chart) {
            var data = chart.data;
            if (data.labels.length && data.datasets.length) {
                return data.labels.map(function(label, i) {
                    var meta = chart.getDatasetMeta(0);
                    var ds = data.datasets[0];
                    var arc = meta.data[i];
                    var custom = arc && arc.custom || {};
                    var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
                    var arcOpts = chart.options.elements.arc;
                    var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
                    var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
                    var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);

                    // We get the value of the current label
                    var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];

                    return {
                        // Instead of `text: label,`
                        // We add the value to the string
                        text: label + ": " + value,
                        fillStyle: fill,
                        strokeStyle: stroke,
                        lineWidth: bw,
                        hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
                        index: i
                    };
                });
            } else {
                return [];
            }
        },

        // -- Validadores
        isEmpty: function (elemento) {
            if (angular.isUndefined(elemento)) {
                return true;
            }
            if (elemento == null) {
                return true;
            }
            if (!elemento instanceof Array) {
                if (elemento.trim() == "") {
                    return true;
                }
            }
            return false;
        },
        isEMailValid: function(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        isTime: function(data) {
            var dataSplited = data.split(":");
            if (dataSplited.length != 3) {
                return false;
            }
            if (dataSplited[0] < 0 || dataSplited[0] > 23) {
                return false;
            }
            if (dataSplited[1] < 0 || dataSplited[1] > 59) {
                return false;
            }
            if (dataSplited[2] < 0 || dataSplited[2] > 59) {
                return false;
            }
            return true;
        },

        // -- Usuario
        clearLocalStorage: function() {
            localStorage.removeItem("vaaDatos");
            localStorage.clear();
        },

        // -- Datos precargados
        getOptionsYesNo: function() {
            var opciones = [];
            opciones.push({ value: "SI", label: "SI"});
            opciones.push({ value: "NO", label: "NO" });
            return opciones;
        },

        getOperationMode: function() {
            var opciones = [];
            opciones.push({ value: "AUTOMATICO", label: "Automático"});
            opciones.push({ value: "MANUAL", label: "Manual" });
            return opciones;
        },

        getOptionsOperationMode: function() {
            var opciones = [];
            opciones.push({ value: "DIURNO", label: "Diurno"});
            opciones.push({ value: "NOCTURNO", label: "Nocturno" });
            return opciones;
        },

        getOptionsTTS: function() {
            var opciones = [];
            opciones.push({ value: "D", label: "Deshabilitado"});
            opciones.push({ value: "N", label: "Sólo para los Nombres" });
            opciones.push({ value: "A", label: "Para Todas las Locuciones" });
            return opciones;
        },

        getMapQueryValidation: function() {
            var opciones = [];
            opciones.push({ par_name:'FAX_DAY', query_id: 1 });
            opciones.push({ par_name:'FAX_NIGHT', query_id: 2 });
            opciones.push({ par_name:'GI_ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_2_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_ACTION_3_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'ACTION_2_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'ACTION_3_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_2_NO_ASW_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_CEL_ACTION_3_NO_ASW_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_2_NO_ASW_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'GI_SEC_ACTION_3_NO_ASW_NIGHT', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_2_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'SEC_ACTION_3_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_1_DAY', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_1_NIGHT', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_2_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_2_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_2_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_2_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_3_BUSY_DAY', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_3_BUSY_NIGHT', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_3_NO_ANSWER_DAY', query_id: 3 });
            opciones.push({ par_name:'CEL_ACTION_3_NO_ANSWER_NIGHT', query_id: 3 });
            opciones.push({ par_name:'ACTION_ERROR_DTMF_DAY', query_id: 4 });
            opciones.push({ par_name:'ACTION_ERROR_DTMF_NIGHT', query_id: 4 });
            opciones.push({ par_name:'ACTION_ERROR_VOICE_DAY', query_id: 4 });
            opciones.push({ par_name:'ACTION_ERROR_VOICE_NIGHT', query_id: 4 });
            opciones.push({ par_name:'ACTION_SILENCE_DAY', query_id: 4 });
            opciones.push({ par_name:'ACTION_SILENCE_NIGHT', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_ERROR_DTMF_DAY', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_ERROR_DTMF_NIGHT', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_ERROR_VOICE_DAY', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_ERROR_VOICE_NIGHT', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_SILENCE_DAY', query_id: 4 });
            opciones.push({ par_name:'GI_ACTION_SILENCE_NIGHT', query_id: 4 });
            opciones.push({ par_name:'VOICE_MAIL_DAY', query_id: 5 });
            opciones.push({ par_name:'VOICE_MAIL_NIGHT', query_id: 6 });
            return opciones;
        },

        // -- Armado de entorno
        processingStage: function() {
            $rootScope.login = $location.path().indexOf("/login") != -1 || $location.url() == "/";
        },
        getMenu: function(itemActivo) {
            $rootScope.menu = [];
            $rootScope.menu.push(new MenuItem("Inicio", itemActivo == 'Inicio', function() { $rootScope.goto('home'); }, undefined, 'btnInicio' ));
            $rootScope.menu.push(new MenuItem("Departamentos", itemActivo == 'Departamentos', function() { $rootScope.goto('departamentos'); }, undefined, 'btnDepartamentos' ));
            $rootScope.menu.push(new MenuItem("Personas", itemActivo == 'Personas', function() { $rootScope.goto('personas'); }, undefined, 'btnPersonas' ));
            $rootScope.menu.push(new MenuItem("Faxes", itemActivo == 'Faxes', function() { $rootScope.goto('faxes'); }, undefined, 'btnFaxes' ));
            $rootScope.menu.push(new MenuItem("Voice Mails", itemActivo == 'Voices', function() { $rootScope.goto('voice_mails'); }, undefined, 'btnVoiceMails' ));
            $rootScope.menu.push(new MenuItem("Bandas Horarias", itemActivo == 'BandasHorarias', function() { $rootScope.goto('bandas_horarias'); }, undefined, 'btnBandasHorarias' ));
            $rootScope.menu.push(new MenuItem("Feriados", itemActivo == 'Feriados', function() { $rootScope.goto('feriados'); }, undefined, 'btnFeriados' ));
            $rootScope.menu.push(new MenuItem("Parámetros", itemActivo == 'Parametros', function() { $rootScope.goto('parametros'); }, undefined, 'btnParametros' ));
            $rootScope.menu.push(new MenuItem("Estadísticas", itemActivo == 'Estadisticas', function() { $rootScope.goto('estadisticas'); }, undefined, 'btnEstadisticas' ));
            $rootScope.menu.push(new MenuItem("Archivos de Audio", itemActivo == 'Archivos', function() { $rootScope.goto('archivos'); }, undefined, 'btnArchivos' ));
        },
        getMenuEDS: function(itemActivo, isHome, isUsuariosPropios) {
            $rootScope.menu = [];
            if (isHome || isUsuariosPropios) {
                $rootScope.menu.push(new MenuItem("Plantilla de Bandas horarias", itemActivo == 'BandasHorarias', function() { $rootScope.goto('bandas_horarias'); } ));
                $rootScope.menu.push(new MenuItem("Plantilla de Feriados", itemActivo == 'Feriados', function() { $rootScope.goto('feriados'); } ));
                $rootScope.menu.push(new MenuItem("Plantilla de Parámetros", itemActivo == 'Parametros', function() { $rootScope.goto('parametros'); } ));
                $rootScope.menu.push(new MenuItem("Estadísticas Generales", itemActivo == 'Estadisticas', function() { $rootScope.goto('estadisticas'); } ));
            } else {
                $rootScope.menu.push(new MenuItem("Inicio", itemActivo == 'Inicio', function() { $rootScope.goto('home'); } ));
                $rootScope.menu.push(new MenuItem("Departamentos", itemActivo == 'Departamentos', function() { $rootScope.goto('departamentos'); } ));
                $rootScope.menu.push(new MenuItem("Personas", itemActivo == 'Personas', function() { $rootScope.goto('personas'); } ));
                $rootScope.menu.push(new MenuItem("Faxes", itemActivo == 'Faxes', function() { $rootScope.goto('faxes'); } ));
                $rootScope.menu.push(new MenuItem("Voice Mails", itemActivo == 'Voices', function() { $rootScope.goto('voice_mails'); } ));
                $rootScope.menu.push(new MenuItem("Bandas Horarias", itemActivo == 'BandasHorarias', function() { $rootScope.goto('bandas_horarias'); } ));
                $rootScope.menu.push(new MenuItem("Feriados", itemActivo == 'Feriados', function() { $rootScope.goto('feriados'); } ));
                $rootScope.menu.push(new MenuItem("Parámetros", itemActivo == 'Parametros', function() { $rootScope.goto('parametros'); } ));
                $rootScope.menu.push(new MenuItem("Estadísticas", itemActivo == 'Estadisticas', function() { $rootScope.goto('estadisticas'); } ));
                $rootScope.menu.push(new MenuItem("Archivos de Audio", itemActivo == 'Archivos', function() { $rootScope.goto('archivos'); }, undefined, 'btnArchivos' ));
            }
        },
		
		hasNumbers: function(value) {
            var regex = /^[a-zA-Z]+$/;
			return (!regex.test(value));
        },
		
		hasUppercase: function(value) {
            var regex = /[A-Z]/;
			return regex.test(value);
        }
    }

});

function MenuItem(label, activo, action, separar, id) {
    return {
        label: label,
        activo: activo,
        action: action,
        separar: separar,
        id: id
    };
}