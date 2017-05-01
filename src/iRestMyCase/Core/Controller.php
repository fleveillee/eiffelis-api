<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-22
 * Time: 23:53
 */

namespace iRestMyCase\Core;

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
	 * @param object $model
	 */
	public function restAction($model)
	{
		$httpMethod = $_SERVER['REQUEST_METHOD'];

		switch ($httpMethod) {
			case 'PUT':
				Renderer::renderJsonResponse(ORM::update($model));
				break;
			case 'POST':
				Renderer::renderJsonResponse(ORM::create($model));
				break;
			case 'GET':
				Renderer::renderJsonResponse(ORM::read($model));
				break;
			case 'HEAD':
				//Send 200 header if found, 404 header if not found (no response body)
				$results = ORM::read($model);
				if (empty($results)) {
					header('HTTP/1.1 404 NOT FOUND');
				} else {
					header('HTTP/1.1 200 OK');
					header('Content-Type: application/json');
				}
				break;
			case 'DELETE':
				Renderer::renderJsonResponse(ORM::delete($model));
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
