<?php

namespace iRestMyCase\ModelGenerator;

use Exception;


use iRestMyCase\Core\DAO\MySQL;
use iRestMyCase\Core\Models\Config;
use iRestMyCase\Core\ORM;

/**
 * Class Utilities
 * @package iRestMyCase\ModelGenerator
 */
class Utilities
{
	/**
	 * Generates a PHP class model instance from a MySQL schema table
	 * @param       $modelName
	 * @param       $tableName
	 */
	public static function generateModelFromMySQL(string $modelName, string $tableName)
	{
		/** @var MySQL $dao */
		$dao = ORM::getDAOinstance('MySQL');
		$tableDesc = $dao->getTableDesc($tableName);

		$ModelGenerator = new ModelGenerator($tableName, $tableDesc->columns());

		$file = self::createFile($modelName);
		$fileContent = $ModelGenerator->getClassOverture();
		$fileContent .= $ModelGenerator->getDaoConst($dao, $tableDesc);
		$fileContent .= $ModelGenerator->getClassProperties($tableDesc->columns());
		$fileContent .= $ModelGenerator->getClassMethods($tableDesc->columns());
		$fileContent .= $ModelGenerator->getClassClosure();
		fwrite($file, $fileContent);
		fclose($file);
	}


	/**
	 * Dispatcher for Generating a PHP class Model based on its corresponding DAO
	 * @param $daoName
	 * @param $modelName
	 * @param $key
	 * @throws Exception
	 */
	public static function generateModel(string $daoName, string $modelName, string $key)
	{

		if ($daoName == 'MySQL') {
			self::generateModelFromMySQL($modelName, $key);
		} else {
			throw new Exception("Unknow DAO for $modelName");
		}

	}

	/**
	 * Creates a physical PHP file for the Generated Model
	 * @param $modelName
	 * @return resource
	 * @throws Exception
	 */
	public static function createFile($modelName)
	{
		$modelGeneratorConfig = Config::getModule('ModelGenerator');

		$filePath = $modelGeneratorConfig["outputFolder"];
		$fileName = $modelName . ".php";

		if (!file_exists($filePath)) {
			if (!mkdir($filePath, 0755, true)) {
				throw new Exception("Unable to create folder for writing generated Models");
			}
		}

		return fopen($filePath . $fileName, 'w');
	}


}
