<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-22
 * Time: 23:06
 */

namespace iRestMyCase\Core;


use Exception;
use iRestMyCase\Core\Models\Config;

class Dispatcher
{
	protected $config;

	public function __construct()
	{
		$this->config = parse_ini_file('config.ini');
	}

	/**
	 * Render given URI
	 * @param string $URI
	 *
	 */
	public static function renderURI($URI)
	{
		$controller = new Controller();

		// Check if we are at @Root URI
		if (strlen($URI) == 0 || $URI == '/') {
			$controller->index();
		} elseif (strlen($URI) > 0) {
			$splitUri = explode('/', $URI);

			if (strlen($splitUri[0]) == 0) {
				array_shift($splitUri);
			}

			try {
				self::attemptRestAction($splitUri);
			} catch (Exception $exception) {

				if (!self::attemptModuleAction($splitUri)) {
					Renderer::renderHttpErrorResponse($exception->getCode(), $exception->getMessage());
				}
			}

		}
	}

	/**
	 * Attempt to render a REST service action for the given URI
	 * @param $splitUri
	 * @throws Exception
	 */
	public static function attemptRestAction($splitUri)
	{
		$rootNamespace = Config::rootNamespace();
		$modelName = $splitUri[0];
		$className = $rootNamespace . '\\Models\\' . $modelName;

		if (class_exists($className)) {
			try {
				$dao = DAO::getDAO($modelName);
			} catch (Exception $exception) {
				Renderer::renderHttpErrorResponse(500, $exception->getMessage());

				return;
			}

			$model = new $className();

			$controller = new Controller();
			$controller->restAction($model, $dao);
		} else {
			throw new Exception("Model Not Found \"$modelName\"", 404);
		}

	}


	public static function attemptModuleAction($splitUri)
	{
		$rootNamespace = Config::rootNamespace();
		$moduleName = $splitUri[0];
		$moduleControllername = $rootNamespace . '\\' . $moduleName . '\\Controller';
		//var_dump($className);
		//echo 'will check@!';
		if (class_exists($moduleControllername)) {
			$action = $splitUri[1];
			if (empty($action)) {
				$action = 'index';
			}

			$controller = new $moduleControllername;
			$controller->$action();

			return true;
		}

		return false;
	}

}
