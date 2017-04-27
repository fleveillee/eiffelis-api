<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-23
 * Time: 00:13
 */

namespace iRestMyCase\ModelGenerator;

use iRestMyCase\Core\DAO\MySQL\TableDesc;
use iRestMyCase\Core\Interfaces\DaoInterface;
use iRestMyCase\Core\Models\Config;

class ModelGenerator
{
	protected $className;
	protected $classDbFields;
	protected $classExtends = '';
	protected $extendedClassName;
	protected $tableName;

	static $_integerTypes = ['bigint', 'int', 'integer', 'mediumint', 'smallint', 'tinyint'];
	static $_numericTypes = [
		'bigint',
		'decimal',
		'double',
		'float',
		'int',
		'integer',
		'mediumint',
		'numeric',
		'smallint',
		'tinyint'
	];
	static $_stringTypes = ['blob', 'char', 'text', 'varchar'];
	static $_enumTypes = ['enum'];
	static $_elseifException = "elseif(isset(\$value)){\n\t\t\tthrow new Exception(\"Invalid Value '\$value' for @propName@ property in @modelName@ model \");\n\t\t}";

	function __construct($tableName = null, $classDbFields = null)
	{
		if (isset($tableName)) {
			$this->tableName = $tableName;
			$this->className($tableName);
		}
		if (isset($classDbFields)) {
			$this->classDbFields($classDbFields);
		}
	}

	public function className($value = null)
	{
		if (!empty($value)) {
			if (strpos($value, 'v_') === 0) {
				$value = substr($value, 2);
			}
			$className_array = explode('_', $value);

			for ($i = 0; $i < count($className_array); $i++) {
				$className_array[$i] = ucfirst($className_array[$i]);
			}
			$this->className = implode('', $className_array);;
		}

		return $this->className;
	}

	public function classDbFields($value = null)
	{
		if (!empty($classDbFields) && is_array($classDbFields)) {
			$this->classDbFields = $value;
		}

		return $this->classDbFields;
	}

	public function extendedClassName($value = null)
	{
		if (!empty($value)) {
			$this->extendedClassName = $value;
		}

		return $this->extendedClassName;
	}

	public function getClassOverture()
	{
		$classExtends = '';
		if (!empty($this->extendedClassName)) {
			$classExtends = " extends $this->extendedClassName";
		}

		$namespace = Config::appName() . '\PublicModels';

		return "<?php
namespace  $namespace;

use Exception;

class $this->className$classExtends
{";

	}

	public function getDaoConst(DaoInterface $dao, TableDesc $tableDesc)
	{
		$daoName = $dao->getName();

		$daoConst = "\n\tconst DAO = '$daoName';";

		if ($daoName == "MySQL") {
			$tableName = $tableDesc->tableName();

			$daoConst .= "\n\tconst TABLE_NAME = '$tableName';";
			foreach ($tableDesc->columns() as &$column) {

				if ($column['Key'] == 'PRI' || strcasecmp($column['Field'], 'id') === 0) {
					$primaryKey = $column['Field'];
					$daoConst .= "\n\tconst PRIMARY_KEY = '$primaryKey';";
				}
			}
		}

		return $daoConst;
	}


	public function getClassClosure()
	{
		return "
}
";
	}


	public function getClassProperties($tableColumns)
	{
		$propertiesContent = "";
		$enumConst = "";

		foreach ($tableColumns as &$column) {

			if (in_array($column['ShortType'], self::$_enumTypes)) {
				$constName = strtoupper($column['Field']) . '_ENUM_VALUES';
				$enumConst .= "	const $constName = [$column[EnumValuesString]];\n";
			}

			$propertyValue = '';
			if (!empty($column['Default'])) {
				if (in_array($column['ShortType'], array_merge(self::$_stringTypes, self::$_enumTypes))) {
					$propertyValue = " = '$column[Default]'";
				} else {
					$propertyValue = " = $column[Default]";
				}
			} elseif (in_array($column['ShortType'],
					self::$_stringTypes) && $column['Null'] == 'NO' && empty($column['Key'])
			) {
				$propertyValue = " = ''";
			}
			$propertiesContent .= "
	protected \$$column[Field]$propertyValue;";
		}

		return $enumConst . "\n" . $propertiesContent . "\n";
	}


	function getClassMethods($tableColumns)
	{
		$methodsContent = "

	public function __construct(\$param=NULL)
	{
";

		if (!empty($this->extendedClassName)) {
			$methodsContent .= "\t\tparent::__construct();\n";
		}

		$methodsContent .= "
		if(isset(\$param)){
			if(is_array(\$param)){
				foreach(\$param as \$key => \$value){
					if(method_exists(get_class(\$this), \$key)){
						\$this->{\$key}(\$value);
					}
					else{
						throw new Exception('Trying to set parameter \"'.\$key.'\" that does not exist in \"'.get_class(\$this).'\" class .');
					}
				}
			}
			else{
				\$this->{self::PRIMARY_KEY}(\$param);";
		if (!empty($this->extendedClassName)) {
			$methodsContent .= "\n\t\t\t\t\$this->load();";
		}
		$methodsContent .= "
			}
		}
	}
";
		$exception = "\n\t\t" . str_replace('@modelName@', $this->className, self::$_elseifException);

		foreach ($tableColumns as &$column) {
			$valueCheck = '';
			$typecastBegin = '';
			$typecastEnd = '';
			$elseif = '';
			$columnException = str_replace('@propName@', $column['Field'], $exception);

			if (in_array($column['ShortType'], self::$_integerTypes)) {
				$valueCheck = '&& (is_int($value) || ctype_digit(trim($value)))';
				$typecastBegin = 'intval(';
				$typecastEnd = ')';
				if ($column['ShortType'] == 'tinyint' || $column['MaxSize'] == 1) {
					$elseif = "
		elseif(\$value === true){
			\$this->$column[Field] = 1;
		}
		elseif(\$value === false){
			\$this->$column[Field] = 0;
		}";
				}
				$elseif .= $columnException;
			} elseif (in_array($column['ShortType'], self::$_numericTypes)) {
				$valueCheck = '&& is_numeric(trim($value))';
				$elseif .= $columnException;
			} elseif (in_array($column['ShortType'], self::$_enumTypes)) {
				$valueCheck = '&& in_array(trim($value), self::$_' . $column['Field'] . 'EnumValues)';
				$typecastBegin = 'trim(';
				$typecastEnd = ')';
				$elseif .= $columnException;
			} elseif (in_array($column['ShortType'], self::$_stringTypes)) {
				$columnNames = [];
				foreach ($tableColumns as $tmpColumn) {
					$columnNames[] = $tmpColumn['Field'];
				}
				if (substr($column['Field'], -3) == '_en' && in_array(substr($column['Field'], 0, -3) . '_fr',
						$columnNames)
				) {
					$bilingualMethodName = substr($column['Field'], 0, -3);

					$methodsContent .= "
	public function $bilingualMethodName()
	{
		return lang(\$this->{$bilingualMethodName}_fr(), \$this->{$bilingualMethodName}_en());
	}
";
				}

				if (isset($column['MaxSize'])) {
					$valueCheck = "&& strlen(trim(\$value)) <= $column[MaxSize]";
					$elseif .= $columnException;
				}
				$typecastBegin = 'trim(';
				$typecastEnd = ')';
			}


			$methodsContent .= "
	public function $column[Field](\$value = NULL)
	{
		if(!empty(\$value)$valueCheck){
			\$this->$column[Field] = $typecastBegin\$value$typecastEnd;
		}$elseif
		return \$this->$column[Field];
	}
";
		}

		return $methodsContent;
	}

}
