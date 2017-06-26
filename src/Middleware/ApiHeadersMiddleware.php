<?php

namespace Wallace\Middleware;

use \Slim\Middleware;

class ApiHeadersMiddleware extends Middleware
{

    public function call()
    {
        $app = $this->app;
        $this->next->call();

        if ($app->request->getMethod() === 'GET') {
            $app->response->headers->set('Content-Type', 'application/json');
        }
    }
}
