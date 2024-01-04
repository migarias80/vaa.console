<?php

namespace providers;

class UserSecurityProvider
{

    function __construct() {

    }

    function VerifyPermissions($accion, $funciones) {
        return true;
    }
}