<?php

namespace iRestMyCase;

class Autoloader{
     private static $searchPath;
    /**
     * Enregistre notre autoloader
     */
    static function register($searchPath){
          self::$searchPath = $searchPath;
          spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Inclue le fichier correspondant à notre classe
     * @param $class string Le nom de la classe à charger
     */
    static function autoload($class){

          $class = str_replace('\\', '/', $class);
          $file = self::$searchPath . $class . '.php';
          //var_dump('AL::'.$file);
          if (file_exists($file)) {
               require_once self::$searchPath . $class . '.php';
          }
    }

}
