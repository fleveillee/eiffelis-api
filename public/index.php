<?php

//phpinfo();
//exit;

//echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// TODO: Remove/comment  once Dev is over
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = parse_ini_file('../config.ini', true);

$libsPath = $config['generic']['libsRelPath'];

require_once $libsPath .'iRestMyCase/Autoloader.php';


iRestMyCase\Autoloader::register($libsPath);

use iRestMyCase\Models\HttpResponse;
use iRestMyCase\Renderer;



$requestUri = $_SERVER['REQUEST_URI'];


if(strlen($requestUri)> 0){

     $splitUri = explode('/', $requestUri);
     if(strlen($splitUri[0]) == 0){
          array_shift($splitUri);
     }

     if(!empty($splitUri[0])){
          $rootNamespace = $config['generic']['rootNamespace'];
          $modelName = $splitUri[0];
          $className = $rootNamespace . '\\Models\\'. $modelName;
          //var_dump($className);
          //echo 'will check@!';
          if(class_exists($className)){
               //echo 'We in !';
               if(!empty($config['models'][$modelName]['dao'])){
                    $daoName = $rootNamespace . '\\DAO\\'. $config['models'][$modelName]['dao'];
               }
               else{
                    $daoName = $rootNamespace . '\\DAO\\'. $config['generic']['defaultDao'];
               }

               if(!class_exists($daoName)){
                    $httpResponse = new HttpResponse();
                    $httpResponse->statusCode(500);
                    $httpResponse->messageBody("Http Error 500: DAO configuration for $modelname is not valid");
                    Renderer::renderHttpResponse($httpResponse);
               }
               $dao = new $daoName();
               $model = new $className();

               processAction($model, $dao);
               echo 'AOK';
          }else{
               $httpResponse = new HttpResponse();
               $httpResponse->statusCode(404);
               $httpResponse->messageBody("Http Error 404: Model Not Found \"$modelName\"");
               Renderer::renderHttpResponse($httpResponse);
          }
     }else{
          $httpResponse = new HttpResponse();
          $httpResponse->statusCode(400);
          $httpResponse->messageBody('Http Error 400: Missing Model Name in URI');
          Renderer::renderHttpResponse($httpResponse);
     }

}

function processAction($model, $dao)
{
     $httpMethod = $_SERVER['REQUEST_METHOD'];
     //var_dump($httpMethod);

     switch ($httpMethod) {
          case 'PUT':
               $dao->update($model);
               break;
          break;
          case 'POST':
               $dao->create($model);
               break;
          case 'GET':
               $dao->read($model);
               break;
          case 'HEAD':
               $dao->read($model);
               //TODO: send 200 header if found, 404 header if not found (no response body)
               break;
          case 'DELETE':
               $dao->delete($model);
               break;
          case 'OPTIONS':
               // TODO: send options response
               $httpResponse = new HttpResponse();
               $httpResponse->statusCode(501);
               $httpResponse->messageBody('Http Error 501: Feature Not Implemented Yet');
               Renderer::renderHttpResponse($httpResponse);
               break;
          default:
               $httpResponse = new HttpResponse();
               $httpResponse->statusCode(405);
               $httpResponse->messageBody('Http Error 405: Method Not Allowed');
               Renderer::renderHttpResponse($httpResponse);
               break;
     }

}



