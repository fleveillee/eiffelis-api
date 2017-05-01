<?php


namespace iRestMyCase\Core;

use Exception;
use iRestMyCase\Core\Interfaces\DaoInterface;
use iRestMyCase\Core\Models\Config;

class ORM
{
	private const MODEL_NOT_FOUND_ERROR_TEXT = "Model Not FOund";

	public static function getName($modelName = null)
	{
		if (isset($modelName) && !empty(Config::models()[$modelName]['dao'])) {
			return Config::models()[$modelName]['dao'];
		} else {
			return Config::defaultDao();
		}
	}

	public static function getDAOinstance(string $daoName): DaoInterface
	{
		$daoFullName = Config::appName() . '\\Core\\DAO\\' . $daoName;

		if (!class_exists($daoFullName)) {
			throw new Exception("DAO class $daoName is not found");
		}

		$daoConfig = Config::dao();

		if (!empty($daoConfig[$daoName])) {
			return new $daoFullName($daoConfig[$daoName]);
		} else {
			return new $daoFullName();
		}
	}

	public static function getModelInstance($modelName)
	{
		if (self::modelExists($modelName)) {
			$className = self::getClassNameFromModelName($modelName);

			return new $className();
		} else {
			throw new Exception(self::MODEL_NOT_FOUND_ERROR_TEXT);
		}

	}

	public static function modelExists($modelName): bool
	{
		$className = self::getClassNameFromModelName($modelName);
		if (class_exists($className)) {
			return true;
		} else {
			return false;
		}
	}

	private static function getClassNameFromModelName($modelName): string
	{
		$appName = Config::appName();

		return $appName . '\\PublicModels\\' . $modelName;
	}

	public static function getModelInstanceFromPrimaryKey($modelName, $primaryKeyValue)
	{
		$modelInstance = self::getModelInstance($modelName);

		//$modelInstance->{'set' . ucfirst($modelInstance::PRIMARY_KEY)}($primaryKeyValue);
		$modelInstance->{$modelInstance::PRIMARY_KEY}($primaryKeyValue);

		$dao = self::getDAOinstance($modelInstance::DAO);
		$dao->read($modelInstance);

		return $modelInstance;
	}


	public static function create($modelInstance): int
	{
		$daoInstance = self::getDAOinstance($modelInstance::DAO);
		return $daoInstance->create($modelInstance);
	}

	public static function read($modelInstance)
	{
		$daoInstance = self::getDAOinstance($modelInstance::DAO);
		return $daoInstance->read($modelInstance);
	}

	public static function update($modelInstance)
	{
		$daoInstance = self::getDAOinstance($modelInstance::DAO);
		return $daoInstance->update($modelInstance);
	}

	public static function delete($modelInstance): int
	{
		$daoInstance = self::getDAOinstance($modelInstance::DAO);
		return $daoInstance->delete($modelInstance);
	}


	public static function getAvailableModelNames($daoName)
	{
		$daoInstance = self::getDAOinstance($daoName);

		return $daoInstance->getAvailableModelNames();
	}

}
