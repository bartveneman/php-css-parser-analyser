<?php

if (!defined('ENV')) {
    exit;
}

$app = new \Slim\Slim();
$app->config([
    'mode' => ENV,
    'debug' => ENV == 'development',
    'log.enabled' => ENV == 'development'
]);

/**
 * Some basic site-wide settings
 */
$app->site = new \stdClass;
$app->site->baseurl = $app->request->getRootUri() . $app->request->getResourceUri();
