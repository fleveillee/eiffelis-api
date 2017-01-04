<?php

namespace iRestMyCase\ModelGenerator;

use Exception;

use iRestMyCase\Core\DAO;
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
		$params = ["daos" => Config::dao()];

		if (isset($_POST["dao"])) {
			$daoName = $_POST["dao"];
			try {
				$dao = DAO::getDAO($daoName);
				$params["models"] = $dao->getModels();
				$params["selectedDao"] = $daoName;
			} catch (Exception $exception) {
				Renderer::renderHttpErrorResponse(404, $exception->getMessage());

				return;
			}
		}

		if (isset($_POST["models"]) && isset($dao)) {
			$models = $_POST["models"];
			$params["selectedModels"] = $models;
			var_dump($models);

			//Utilities::generateModelsFromMySQLTables();

			foreach ($models as $key => $modelName) {
				try {
					Utilities::generateModel($dao, $modelName, $key);
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
