<?php

namespace iRestMyCase\Core\Models;

class Config
{
     static private $environmentType = "prod";
     static private $librairiesRelativePath = "../src/";
     static private $rootNamespace = 'iRestMyCase';
     static private $defaultDao = "MySQL";
     static private $dao;
     static private $models;

     public static function load(?array $config = null){

          if(isset($config['generic'])){
               self::loadGenericConfig($config['generic']);
          }

          if(isset($config['dao'])){
               self::dao($config['dao']);
          }

          if(isset($config['models'])){
               self::models($config['models']);
          }


     }

     private static function loadGenericConfig(array $genericConfig) {
          if(isset($genericConfig['environmentType'])){
               self::$environmentType = $genericConfig['environmentType'];
          }
          if(isset($genericConfig['librairiesRelativePath'])){
               self::$librairiesRelativePath = $genericConfig['librairiesRelativePath'];
          }
          if(isset($genericConfig['rootNamespace'])){
               self::$rootNamespace = $genericConfig['rootNamespace'];
          }
          if(isset($genericConfig['defaultDao'])){
               self::$defaultDao = $genericConfig['defaultDao'];
          }
          if(isset($genericConfig['librairiesRelativePath'])){
               self::$librairiesRelativePath = $genericConfig['librairiesRelativePath'];
          }

     }

     public static function environmentType(?string $value = null) : ?string{
          if(isset($value)){
               self::$environmentType = $value;
               self::setDebugOptions(self::$environmentType);
          }
          return self::$environmentType;
     }

     public static function environmentIsDev() : bool{
          return self::environmentType() == 'dev';
     }

     public static function setDebugOptions(string $environmentType){
          if( $environmentType == 'dev'){
               ini_set('display_errors', 1);
               ini_set('display_startup_errors', 1);
               error_reporting(E_ALL);
          }
     }

     public static function librariesRelativePath(?string $value = null) : ?string{
          if(isset($value)){
               self::$librairiesRelativePath = $value;
          }
          return self::$librairiesRelativePath;
     }

     public static function rootNamespace(?string $value = null) : ?string{
          if(isset($value)){
               self::$rootNamespace = $value;
          }
          return self::$rootNamespace;
     }

     public static function dao(?array $value = null) : ?array{
          if(isset($value)){
               self::$dao = $value;
          }
          return self::$dao;
     }

     public function getDao(string $daoName): array
     {
          return empty(self::$dao[$daoName])? null: self::$dao[$daoName];
     }

     public static function models(?array $value = null) : ?array{
          if(isset($value)){
               self::$models = $value;
          }
          return self::$models;
     }


}