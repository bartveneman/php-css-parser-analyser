<?php

namespace Wallace\Middleware;

use \Slim\Middleware;
use \Slim\Slim;
use Wallace\Models\Persistent\User;
use Wallace\Exceptions\AuthException;

class AuthMiddleware extends Middleware
{

    private function authenticate($apikey)
    {
        $user = User::find_by_apikey($apikey);
        $app = Slim::getInstance();

        if (!$user) {
            throw new AuthException();
        }
    }

    public function call()
    {
        $app = Slim::getInstance();
        $apikey = $app->request->get('access_token');

        $this->authenticate($apikey);
        $this->next->call();
    }
}
