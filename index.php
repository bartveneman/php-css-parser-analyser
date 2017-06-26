<?php

namespace Wallace;

use Wallace\Controllers\ImportsController;
use Wallace\Controllers\UsersController;
use Wallace\Middleware\ApiHeadersMiddleware;
use Wallace\Middleware\AuthMiddleware;
use Wallace\Exceptions\AuthException;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/sentry/sentry/lib/Raven/Autoloader.php';
require __DIR__ . '/src/config/env.config.php';
require __DIR__ . '/src/config/app.config.php';
require __DIR__ . '/src/config/slim.config.php';
require __DIR__ . '/src/config/activerecord.config.php';

$app->add(new ApiHeadersMiddleware());
$app->add(new AuthMiddleware());

/**
 * Routing and controlling
 */
$app->group(
    '/v1',
    function () use ($app) {

        $app->group(
            '/imports',
            function () use ($app) {
                $importsController = new ImportsController();

                $app->get(
                    '/:identifier',
                    function ($identifier) use ($app, $importsController) {
                        return $importsController->show($identifier);
                    }
                );

                $app->post(
                    '/:identifier',
                    function ($identifier) use ($app, $importsController) {
                        $importsController->create(
                            $identifier,
                            $app->request->getBody()
                        );
                        $app->response->setStatus(201);
                    }
                );
            }
        );

        $app->group(
            '/users',
            function () use ($app) {
                $usersController = new UsersController();

                $app->post(
                    '/',
                    function () use ($app, $usersController) {
                        $usersController->create(
                            $app->request->post('apikey'),
                            $app->request->post('identifier')
                        );
                        $app->response->setStatus(201);
                    }
                );
            }
        );
    }
);

$app->error(
    function (\Exception $ex) use ($app) {
        if ($ex instanceof AuthException) {
            $app->response->setStatus($ex->getCode());
            $app->response->setBody($ex->getMessage());
            return;
        }

        $sentryClient->captureException($ex);
    }
);

$app->run();
