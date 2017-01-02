<?php


namespace iRestMyCase\Core;

use Exception;
use iRestMyCase\Core\Models\Config;

class DAO{

     public static function getName($modelName = null){
          if(isset($modelName) && !empty(Config::models()[$modelName]['dao'])){
               return Config::models()[$modelName]['dao'];
          }
          else{
               return Config::defaultDao();
          }
     }

     public static function getDAO(string $daoName){

          $daoFullName = Config::rootNamespace() . '\\Core\\DAO\\'. $daoName;

          if(!class_exists($daoFullName)){
               throw new Exception("DAO class $daoName is not found");
          }

          $daoConfig = Config::dao();

          if(!empty($daoConfig[$daoName])){
               return new $daoFullName($daoConfig[$daoName]);
          }
          else {
               return new $daoFullName();
          }
     }


}