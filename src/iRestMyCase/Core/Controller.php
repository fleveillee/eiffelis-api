<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-22
 * Time: 23:53
 */

namespace iRestMyCase\Core;

use Exception;
use iRestMyCase\Core\Models\Config;

class Controller
{

    public function index()
    {
        if(Config::environmentIsDev()){
            self::render('index');
        }
        else{
            Renderer::renderHttpErrorResponse(400, "Missing Model Name in URI");
        }

    }


    public function attemptRestAction($splitUri)
    {
        $rootNamespace = Config::rootNamespace();
        $modelName = $splitUri[0];
        $className = $rootNamespace . '\\Models\\'. $modelName;
        //var_dump($className);
        //echo 'will check@!';
        if(class_exists($className)){
            //echo 'We in !';

            try{
                $dao = DAO::getDAO($modelName);
            }
            catch(Exception $exception){
                Renderer::renderHttpErrorResponse(500, $exception->getMessage());
                return true;
            }

            $model = new $className();

            self::processAction($model, $dao);
            echo 'AOK';
        }else{
            throw new Exception("Model Not Found \"$modelName\"", 404);
        }

    }


    public function attemptModuleAction($splitUri)
    {
        $rootNamespace = Config::rootNamespace();
        $moduleName = $splitUri[0];
        $moduleControllername = $rootNamespace . '\\'. $moduleName . '\\Controller';
        //var_dump($className);
        //echo 'will check@!';
        if(class_exists($moduleControllername)){
            $action = $splitUri[1];
            if(empty($action)){
                $action = 'index';
            }

            $controller = new $moduleControllername;
            $controller->$action();
            return true;
        }
        return false;
    }


    private function processAction($model, $dao)
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        //var_dump($httpMethod);

        switch ($httpMethod) {
            case 'PUT':
                   $dao->update($model);
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
                Renderer::renderHttpResponse(501, "Feature Not Implemented Yet");
                break;
            default:
                Renderer::renderHttpErrorResponse(405, "Method Not Allowed");
                break;
         }

    }

    private function render($viewName)
    {
        include('views/' . $viewName . '.php');
    }


}