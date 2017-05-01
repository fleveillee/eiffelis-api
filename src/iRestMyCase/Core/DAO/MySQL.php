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

class MySQL implements DaoInterface
{
	/** @var PDO * */
	protected $dbh;

	/**
	 * MySQL constructor.
	 * @param array $config
	 * @throws Exception
	 */
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

	/**
	 * Returns the DAO Name
	 * @return string
	 */
	function getName(): string
	{
		return (new \ReflectionClass($this))->getShortName();
	}


	/**
	 * PDO MySQL database connection information
	 * @param string $serverName
	 * @param string $schema
	 * @param string $username
	 * @param string $password
	 */
	function connect(string $serverName, string $schema, string $username, string $password)
	{
		$this->dbh = new PDO("mysql:host=$serverName;dbname=$schema", $username, $password);
		// set the PDO error mode to exception
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Provides a list of available model names for the Model Generator
	 * @return array
	 */
	public function getAvailableModelNames(): array
	{
		$sql = "SHOW TABLES";
		$query = $this->getDbh()->query($sql);
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

	/**
	 * Gets MySQL TableDesc information for model generation
	 * @param string $tableName
	 * @return TableDesc model
	 */
	public function getTableDesc(string $tableName): TableDesc
	{
		return new TableDesc($this->getDbh(), $tableName);

	}

	/**
	 * Get PDO DBH
	 * @return PDO
	 */
	private function getDbh(): PDO
	{
		return $this->dbh;
	}

	/**
	 * Create a persistent entry for the provided model instance
	 * @param $modelInstance
	 * @return int Last Insert ID
	 */
	public function create($modelInstance): int
	{
		$modelArray = $this->objectToCleanArray($modelInstance);
		$sql = 'INSERT INTO ' . $modelInstance::TABLE_NAME . ' (' . implode(', ',
				array_keys($modelArray)) . ') ' . 'VALUES (:' . implode(', :', array_keys($modelArray)) . ')';

		$stmt = $this->getDbh()->prepare($sql);
		$this->bindValues($stmt, $modelArray);

		$stmt->execute();

		return $this->getDbh()->lastInsertId();
	}

	/**
	 * Read From Data Source using properties of the provided model instance
	 * @param $modelInstance
	 * @return array
	 */
	public function read($modelInstance): array
	{
		$stmt = $this->runQuery('SELECT * FROM ' . $modelInstance::TABLE_NAME, $modelInstance);

		return $stmt->fetchAll(PDO::FETCH_CLASS, get_class($modelInstance));
	}

	/**
	 * Update Data Source to match current model instance properties
	 * @param $modelInstance
	 * @return int
	 * @throws Exception
	 */
	public function update($modelInstance)
	{
		if (empty($modelInstance->{$modelInstance::PRIMARY_KEY}())) {
			throw new Exception('Primary Key ' . $modelInstance::PRIMARY_KEY . ' Has No Value. It Must Be Set in Order to Update An Existing Model.');
		}

		// We don't want a clean array here since some previously set values could be updated to NULL values
		$modelArray = json_decode(json_encode($modelInstance), true);
		$sql = 'UPDATE ' . $modelInstance::TABLE_NAME . ' SET ';

		$firstEntry = true;
		foreach (array_keys($modelArray) as $key) {
			if ($key != $modelInstance::PRIMARY_KEY) {
				if ($firstEntry) {
					$firstEntry = false;
				} else {
					$sql .= ', ';
				}
				$sql .= "$key=:$key";
			}
		}

		$sql .= ' WHERE ' . $modelInstance::PRIMARY_KEY . '=:' . $modelInstance::PRIMARY_KEY;

		$stmt = $this->getDbh()->prepare($sql);
		$this->bindValues($stmt, $modelArray);

		$stmt->execute();

		return $stmt->rowCount();
	}

	/**
	 * Returns the List of Available Models for this DAO.
	 * @param object $modelInstance
	 * @return int Number of Entries Affected
	 */
	public function delete($modelInstance): int
	{
		$stmt = self::runQuery('DELETE FROM ' . $modelInstance::TABLE_NAME, $modelInstance);

		return $stmt->rowCount();
	}

	/**
	 * Transfer PHP object in an array and cleans out null values
	 * @param $modelInstance
	 * @return array
	 */
	private function objectToCleanArray($modelInstance): array
	{
		$modelArray = json_decode(json_encode($modelInstance), true);

		return array_filter($modelArray, function ($value) { return !is_null($value); });
	}

	/**
	 * Build MySQL Search Query
	 * @param array $modelArray
	 * @return string
	 */
	private function buildSearchQuery(array $modelArray): string
	{
		$firstEntry = true;
		$sql = ' ';
		foreach ($modelArray as $key => $value) {
			if ($firstEntry) {
				$sql .= 'WHERE ';
				$firstEntry = false;
			} else {
				$sql .= 'AND ';
			}
			$sql .= "$key=:$key ";
		}

		return $sql;
	}

	/**
	 * Binds Values to PDO Statement
	 * @param \PDOStatement $stmt
	 * @param array         $modelArray
	 */
	private function bindValues(\PDOStatement $stmt, array $modelArray)
	{
		foreach ($modelArray as $key => $value) {
			$stmt->bindValue(":$key", $value);
		}
	}

	/**
	 * Run MySQL Query
	 * @param string $sql
	 * @param        $modelInstance
	 * @return \PDOStatement
	 */
	private function runQuery(string $sql, $modelInstance): \PDOStatement
	{
		$modelArray = $this->objectToCleanArray($modelInstance);
		$stmt = $this->getDbh()->prepare($sql . $this->buildSearchQuery($modelArray));
		$this->bindValues($stmt, $modelArray);
		$stmt->execute();

		return $stmt;
	}


}
