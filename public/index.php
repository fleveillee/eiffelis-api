<?php

//echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

require __DIR__ . '/../vendor/autoload.php';

$config = parse_ini_file('../config.ini', true);

use iRestMyCase\Core\Dispatcher;
use iRestMyCase\Core\Models\Config;

Config::load($config);

Dispatcher::renderUri($_SERVER['REQUEST_URI']);
