<?php

namespace iRestMyCase\ModelGenerator;

use Exception;


use iRestMyCase\Core\DAO\MySQL;
use iRestMyCase\Core\Models\Config;
use iRestMyCase\ModelGenerator\Models\TableDesc;

class Utilities{

     public static function generateModelFromMySQL($dao, $modelName, $tableName){

          $tableDesc = $dao->getTableDesc($tableName);

          $ModelGenerator = new ModelGenerator($tableName, $tableDesc->columns());

          $mySqlConfig = Config::getDao('MySQL');

          $file = self::createFile($tableName,  $mySqlConfig["schema"]);
          echo "\t...Creating Class...\n";
          fwrite($file, $ModelGenerator->initClass());
          echo "\t...Adding Class Properties...\n";
          fwrite($file, $ModelGenerator->classProperties($tableDesc->columns()));
          echo "\t...Adding Class Methods...\n";
          fwrite($file, $ModelGenerator->classMethods($tableDesc->columns()));
          echo "\t...Closing Class...\n";
          fwrite($file, $ModelGenerator->closeClass());
          fclose($file);
          echo "Complete!\n";
     }



     public static function generateModel($dao, $modelName, $key){

          if($dao instanceof MySQL){
               self::generateModelFromMySQL($dao, $modelName, $key);
          }
          else{
               throw new Exception("Unknow DAO for $model");
          }

     }

     public static function createFile($tableName, $schema){

     	if(strpos($tableName, 'v_') === 0 ){
     		$filename = substr($tableName, 2);
     	}
     	else{
     		$filename = $tableName;
     	}

     	$dirname = explode('_', $filename);

     	for($i=0; $i<count($dirname); $i++){
     		$dirname[$i] = ucfirst($dirname[$i]);
     	}

     	$filename = array_pop($dirname);

     	if(!empty($dirname) && count($dirname)>0){
     		$dirname = implode('/', $dirname);
     		if (!file_exists($dirname)) {
     			mkdir($dirname, 0755, true);
     		}
     		$filename = $dirname .'/'. $filename;
     	}

     	$filename .= '.php';
     	echo "Generating $filename \nUsing $schema.$tableName\n";
     	return fopen($filename, 'w');
     }



}