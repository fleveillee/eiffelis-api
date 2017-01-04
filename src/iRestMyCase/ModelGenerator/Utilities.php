<?php

namespace iRestMyCase\ModelGenerator;

use Exception;


use iRestMyCase\Core\DAO\MySQL;
use iRestMyCase\Core\Models\Config;

/**
 * Class Utilities
 * @package iRestMyCase\ModelGenerator
 */
class Utilities
{
	/**
	 * Generates a PHP class model instance from a MySQL schema table
	 * @param MySQL $dao
	 * @param $modelName
	 * @param $tableName
	 */
	public static function generateModelFromMySQL(MySQL $dao, string $modelName, string $tableName)
	{
		$tableDesc = $dao->getTableDesc($tableName);

		$ModelGenerator = new ModelGenerator($tableName, $tableDesc->columns());

		$file = self::createFile($modelName);
		$fileContent = $ModelGenerator->initClass();
		$fileContent .= $ModelGenerator->classProperties($tableDesc->columns());
		$fileContent .= $ModelGenerator->classMethods($tableDesc->columns());
		$fileContent .= $ModelGenerator->closeClass();
		fwrite($file, $fileContent);
		fclose($file);
	}


	/**
	 * Dispatcher for Generating a PHP class Model based on its corresponding DAO
	 * @param $dao
	 * @param $modelName
	 * @param $key
	 * @throws Exception
	 */
	public static function generateModel($dao, $modelName, $key)
	{

		if ($dao instanceof MySQL) {
			self::generateModelFromMySQL($dao, $modelName, $key);
		} else {
			throw new Exception("Unknow DAO for $modelName");
		}

	}

	/**
	 * Creates a physical PHP file for the Generated Model
	 * @param $modelName
	 * @return resource
	 */
	public static function createFile($modelName)
	{
		$mySqlConfig = Config::getDao('MySQL');

		$filePath = $mySqlConfig["outputFolder"];
		$fileName = $modelName . ".php";

		if (!file_exists($filePath)) {
			mkdir($filePath, 0755, true);
		}

		return fopen($filePath. $fileName, 'w');
	}


}
