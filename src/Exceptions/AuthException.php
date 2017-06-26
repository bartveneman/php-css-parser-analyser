<?php

namespace Wallace\Exceptions;

class AuthException extends \Exception
{

    public function __construct()
    {
        $this->message = 'Unauthorized. Use a valid access-token.';
        $this->code = 401;
    }
}
