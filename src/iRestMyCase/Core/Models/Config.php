<?php

namespace iRestMyCase\Core\Models;

/**
 * Class Config
 * @package iRestMyCase\Core\Models
 */
class Config
{
	/** @var string * */
	static private $environmentType = "prod";
	/** @var string * */
	static private $librairiesRelativePath = "../src/";
	/** @var string * */
	static private $rootNamespace = 'iRestMyCase';
	/** @var string * */
	static private $defaultDao = "MySQL";
	/** @var array * */
	static private $dao;
	/** @var array * */
	static private $models;
	/** @var array * */
	static private $modules;

	/**
	 * load
	 * @param array|null|null $config Configuration Array
	 */
	public static function load(?array $config = null)
	{

		if (isset($config['generic'])) {
			self::loadGenericConfig($config['generic']);
		}

		foreach ($config as $key => $params) {
			if (method_exists(get_class(), $key)) {
				self::$key($params);
			}
		}

	}

	/**
	 * loadGenericConfig
	 * @param array $genericConfig
	 */
	private static function loadGenericConfig(array $genericConfig)
	{
		if (isset($genericConfig['environmentType'])) {
			self::$environmentType = $genericConfig['environmentType'];
		}
		if (isset($genericConfig['librairiesRelativePath'])) {
			self::$librairiesRelativePath = $genericConfig['librairiesRelativePath'];
		}
		if (isset($genericConfig['rootNamespace'])) {
			self::$rootNamespace = $genericConfig['rootNamespace'];
		}
		if (isset($genericConfig['defaultDao'])) {
			self::$defaultDao = $genericConfig['defaultDao'];
		}
		if (isset($genericConfig['librairiesRelativePath'])) {
			self::$librairiesRelativePath = $genericConfig['librairiesRelativePath'];
		}

	}

	/**
	 * @param null|string|null $value
	 * @return null|string
	 */
	public static function environmentType(?string $value = null): ?string
	{
		if (isset($value)) {
			self::$environmentType = $value;
			self::setPhpDebugOptions(self::$environmentType);
		}

		return self::$environmentType;
	}

	/**
	 * environmentIsDev
	 * @return bool
	 */
	public static function environmentIsDev(): bool
	{
		return self::environmentType() == 'dev';
	}

	/**
	 * Set PHP Debug Options
	 * @param string $environmentType
	 */
	public static function setPhpDebugOptions(string $environmentType)
	{
		if ($environmentType == 'dev') {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
	}

	/**
	 * Get/Set Librairies Relative Path
	 * @param null|string|null $value
	 * @return null|string
	 */
	public static function librariesRelativePath(?string $value = null): ?string
	{
		if (isset($value)) {
			self::$librairiesRelativePath = $value;
		}

		return self::$librairiesRelativePath;
	}

	/**
	 * Get/Set Root Namespace
	 * @param null|string|null $value
	 * @return null|string
	 */
	public static function rootNamespace(?string $value = null): ?string
	{
		if (isset($value)) {
			self::$rootNamespace = $value;
		}

		return self::$rootNamespace;
	}


	/**
	 * Get/Set Default DAO
	 * @param null|string $value
	 * @return null|string
	 */
	public static function defaultDao(?string $value = null): ?string
	{
		if (isset($value)) {
			self::$defaultDao = $value;
		}

		return self::$defaultDao;
	}

	/**
	 * Get/Set DAO Settings array
	 * @param array|null|null $value
	 * @return array|null
	 */
	public static function dao(?array $value = null): ?array
	{
		if (isset($value)) {
			self::$dao = $value;
		}

		return self::$dao;
	}

	/**
	 * Get a specific DAO's settings array
	 * @param string $daoName
	 * @return array
	 */
	public static function getDao(string $daoName): ?array
	{
		return empty(self::$dao[$daoName]) ? null : self::$dao[$daoName];
	}

	/**
	 * Get/Set Models Settings array
	 * @param array|null|null $value
	 * @return array|null
	 */
	public static function models(?array $value = null): ?array
	{
		if (isset($value)) {
			self::$models = $value;
		}

		return self::$models;
	}

	/**
	 * Get/Set Modules Settings array
	 * @param array|null|null $value
	 * @return array|null
	 */
	public static function modules(?array $value = null): ?array
	{
		if (isset($value)) {
			self::$modules = $value;
		}

		return self::$modules;
	}

	/**
	 * Get a specific module's settings array
	 * @param string $moduleName
	 * @return array
	 */
	public static function getModule(string $moduleName): ?array
	{
		return empty(self::$modules[$moduleName]) ? null : self::$modules[$moduleName];
	}

}
