<?php

// Autoload
require_once __DIR__.'/../vendor/silex/autoload.php';

// Silex
$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../src/controllers.php';
$app->run();
