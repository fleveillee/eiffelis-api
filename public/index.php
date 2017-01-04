<?php

//phpinfo();
//exit;

//echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$config = parse_ini_file('../config.ini', true);

$librairiesRelativePath = $config['generic']['librairiesRelativePath'];

require_once $librairiesRelativePath . "iRestMyCase/Core/Autoloader.php";

iRestMyCase\Core\Autoloader::register($librairiesRelativePath);

use iRestMyCase\Core\Dispatcher;
use iRestMyCase\Core\Models\Config;

Config::load($config);

Dispatcher::renderUri($_SERVER['REQUEST_URI']);







