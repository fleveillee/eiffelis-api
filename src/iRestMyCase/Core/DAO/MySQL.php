<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-23
 * Time: 00:00
 */

namespace iRestMyCase\Core\DAO;

use Exception;
use PDO;

use iRestMyCase\Core\Interfaces\DaoInterface;
use iRestMyCase\Core\DAO\MySQL\TableDesc;
use PDOException;


class MySQL implements DaoInterface
{
	/** @var PDO * */
	protected $dbh;

	public function __construct(array $config)
	{
		if (!isset($config['server'])) {
			throw new Exception("MySQL DAO configuration missing 'server' property. Please set 'MySQL[server]' under '[dao]' in config.ini");
		}
		if (!isset($config['schema'])) {
			throw new Exception("MySQL DAO configuration missing 'schema' property. Please set 'MySQL[schema]' under '[dao]' in config.ini");
		}
		if (!isset($config['username'])) {
			throw new Exception("MySQL DAO configuration missing 'username' property. Please set 'MySQL[username]' under '[dao]' in config.ini");
		}
		if (!isset($config['password'])) {
			throw new Exception("MySQL DAO configuration missing 'password' property. Please set 'MySQL[password]' under '[dao]' in config.ini");
		}

		$this->connect($config['server'], $config['schema'], $config['username'], $config['password']);
	}


	function connect($serverName, $schema, $username, $password)
	{
		try {
			$this->dbh = new PDO("mysql:host=$serverName;dbname=$schema", $username, $password);
			// set the PDO error mode to exception
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}

	}

	public function getModelNames(): array
	{
		$sql = "SHOW TABLES";
		$query = $this->dbh->query($sql);
		$tableNames = $query->fetchAll(PDO::FETCH_COLUMN);
		$models = [];
		foreach ($tableNames as &$tableName) {
			$tableNameWords = explode("_", $tableName);
			//Remove 'view' abbreviation prefix
			if ($tableNameWords[0] == "v") {
				array_shift($tableNameWords);
			}
			foreach ($tableNameWords as &$tableNameWord) {
				$tableNameWord = ucfirst($tableNameWord);
			}
			$models[$tableName] = implode("", $tableNameWords);
		}

		return $models;
	}

	public function getTableDesc(string $tableName)
	{
		return new TableDesc($this->getDbh(), $tableName);

	}

	private function getDbh()
	{
		return $this->dbh;
	}

	public function create($model)
	{

	}

	public function read($model)
	{

	}

	public function update($model)
	{

	}

	public function delete($model)
	{

	}


}
