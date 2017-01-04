<?php

namespace iRestMyCase\Core;

class Autoloader{
     private static $searchPath;
    /**
     * Register autoloader
     * @param string $searchPath
     */
    static function register(string $searchPath){
          self::$searchPath = $searchPath;
          spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Autoload Class
     * @param $class string Class Name
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
