<?php

if (!defined('ENV')) {
    exit;
}

\ActiveRecord\Config::initialize(function ($cfg) {
    $cfg->set_model_directory('src/Models/Persistent');
    $cfg->set_connections([
        'development' => 'mysql://root:root@localhost?charset=utf8',
        'production' => 'mysql://user:pass@url?charset=utf8'
    ]);
    $cfg->set_default_connection(ENV);
});
