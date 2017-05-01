<?php

//echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require __DIR__ . '/../vendor/autoload.php';

use iRestMyCase\Core\Dispatcher;
use iRestMyCase\Core\Models\Config;

// Load Configuration
Config::load(parse_ini_file(__DIR__ . '/../config.ini', true));

// Render URI
Dispatcher::renderUri($_SERVER['REQUEST_URI']);
