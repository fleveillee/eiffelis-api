<?php

//phpinfo();
//exit;

//echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$config = parse_ini_file('../config.ini', true);

$librairiesRelativePath = $config['generic']['librairiesRelativePath'];

require_once $librairiesRelativePath .'iRestMyCase/Core/Autoloader.php';

iRestMyCase\Core\Autoloader::register($librairiesRelativePath);

use iRestMyCase\Core\Models\Config;
use iRestMyCase\Core\Renderer;
use iRestMyCase\Core\Controller;

Config::load($config);


$requestUri = $_SERVER['REQUEST_URI'];

$controller = new Controller();

if(strlen($requestUri) == 0 || $requestUri == '/' ){
     $controller->index();
}
elseif(strlen($requestUri)> 0){
     $splitUri = explode('/', $requestUri);

     if(strlen($splitUri[0]) == 0){
          array_shift($splitUri);
     }

     try{
         $controller->attemptRestAction($splitUri);
     }
     catch(Exception $exception){

          if(!$controller->attemptModuleAction($splitUri)){
               Renderer::renderHttpErrorResponse($exception->getCode(), $exception->getMessage());
          }
     }

}





