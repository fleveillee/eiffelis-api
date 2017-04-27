<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-22
 * Time: 23:53
 */

namespace iRestMyCase\Core;

use iRestMyCase\Core\Interfaces\DaoInterface;
use iRestMyCase\Core\Models\Config;

/**
 * Class Controller
 * @package iRestMyCase\Core
 */
class Controller
{

	/**
	 * Default Root Index Action
	 */
	public function index()
	{
		if (Config::environmentIsDev()) {
			self::render('index');
		} else {
			Renderer::renderHttpErrorResponse(400, "Missing Model Name in URI");
		}

	}


	/**
	 * Run a Rest service action
	 * @param object       $model
	 * @param DaoInterface $dao
	 */
	public function restAction($model, DaoInterface $dao)
	{
		$httpMethod = $_SERVER['REQUEST_METHOD'];

		switch ($httpMethod) {
			case 'PUT':
				$dao->update($model);
				break;
			case 'POST':
				$dao->create($model);
				break;
			case 'GET':
				$dao->read($model);
				break;
			case 'HEAD':
				$dao->read($model);
				//TODO: send 200 header if found, 404 header if not found (no response body)
				break;
			case 'DELETE':
				$dao->delete($model);
				break;
			case 'OPTIONS':
				// TODO: send options response
				Renderer::renderHttpErrorResponse(501, "Feature Not Implemented Yet");
				break;
			default:
				Renderer::renderHttpErrorResponse(405, "Method Not Allowed");
				break;
		}

	}

	/**
	 * Render View
	 * @param $viewName
	 */
	private function render($viewName)
	{
		include('views/' . $viewName . '.php');
	}

}
