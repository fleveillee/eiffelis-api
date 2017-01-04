<?php
/**
 * Created by PhpStorm.
 * User: fleveillee
 * Date: 16-02-23
 * Time: 00:13
 */

namespace iRestMyCase\ModelGenerator;


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
			$this->className = implode('_', $className_array);;
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

	public function initClass()
	{
		$classExtends = '';
		if (!empty($this->extendedClassName)) {
			$classExtends = " extends $this->extendedClassName";
		}

		return "<?php

class $this->className$classExtends
{
";

	}

	public function closeClass()
	{
		return "
}
";
	}


	public function classProperties($tableColumns)
	{
		$propertiesContent = "";
		$staticProperties = "	public static \$_tableName = '" . $this->tableName . "';\n";

		foreach ($tableColumns as &$column) {

			if ($column['Key'] == 'PRI' || strcasecmp($column['Field'], 'id') === 0) {
				$staticProperties .= "	public static \$_primaryKey = '$column[Field]';\n";

			}

			if (in_array($column['ShortType'], self::$_enumTypes)) {
				$staticProperties .= "	public static \$_$column[Field]EnumValues = [$column[EnumValuesString]];\n";

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

		return $staticProperties . "\n" . $propertiesContent . "\n";
	}


	function classMethods($tableColumns)
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
				\$this->{self::\$_primaryKey}(\$param);";
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
				$columnNames=[];
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
