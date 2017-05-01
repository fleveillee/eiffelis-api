<?php

namespace iRestMyCase\ModelGenerator;

use Exception;

use iRestMyCase\Core\ORM;
use iRestMyCase\Core\Renderer;
use iRestMyCase\Core\Models\Config;

/**
 * Class Controller
 * @package iRestMyCase\ModelGenerator
 */
class Controller
{

	/**
	 * Model Generator Root Index View
	 */
	public function index()
	{
		$params = [
			"daos" => Config::dao(),
			"languages" => [
				"PHP71" => "PHP 7.1",
				"PHP56" => "PHP 5.6"
			]
		];

		//If DAO is selected, load model names
		if (isset($_POST["dao"])) {
			$daoName = $_POST["dao"];
			try {
				$params["models"] = ORM::getAvailableModelNames($daoName);
				$params["selectedDao"] = $daoName;
			} catch (Exception $exception) {
				Renderer::renderHttpErrorResponse(404, $exception->getMessage());

				return;
			}
		}

		// If models were selected, generate them
		if (isset($_POST["models"]) && isset($daoName)) {
			$models = $_POST["models"];
			$params["selectedModels"] = $models;

			foreach ($models as $key => $modelName) {
				try {
					Utilities::generateModel($daoName, $modelName, $key);
				} catch (Exception $e) {
					// TODO: Manage unknown DAO exception
				}
			}

		}
		self::render('index', $params);
	}


	private function render($viewName, $params)
	{
		foreach ($params as $name => $value) {
			$$name = $value;
		}
		include('views/' . $viewName . '.php');
	}
}
