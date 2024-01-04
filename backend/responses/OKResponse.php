<?php

namespace responses;

class OKResponse extends GenericResponse
{

    function __construct($message, $data=null, $token=null)
    {
        parent::__construct(200);
        parent::SetMessage($message);
        parent::SetData($data);
        parent::SetToken($token);
        parent::SetCode(CODE_OK);
    }

}