<?php

namespace iRestMyCase\Core\DAO\MySQL;

use PDO;

use iRestMyCase\ModelGenerator\ModelGenerator;


class TableDesc{
	protected $dbh;
	protected $tableName;
	protected $columns;

	public function __construct(PDO $dbh, string $tableName){
		$this->dbh = $dbh;
		$this->tableName($tableName);

		$this->load();
	}

	function tableName(?string $value=null): ?string
	{
		if(!empty($value)){$this->tableName = $value;}

		return $this->tableName;
	}

	function columns(): array{
		return $this->columns;
	}

	function load(){
		if(!empty($this->tableName)){
			$sth = $this->dbh->prepare("DESCRIBE ".$this->tableName);
			$sth->execute();
			$this->columns = $sth->fetchAll(PDO::FETCH_ASSOC);
			$this->setShortTypes();
			$this->setEnumValues();
			$this->setMaxSizes();
		}

	}


	protected function setShortTypes()
	{
		for($i=0;$i< count($this->columns);$i++){
			$strpos = strpos($this->columns[$i]['Type'],'(');
			if($strpos !== FALSE){
				$this->columns[$i]['ShortType'] = substr($this->columns[$i]['Type'],0, $strpos);
			}
			else{
				$this->columns[$i]['ShortType'] = $this->columns[$i]['Type'];
			}

		}
	}

	protected function setEnumValues()
	{
		for($i=0;$i< count($this->columns);$i++){

			if(in_array($this->columns[$i]['ShortType'], ModelGenerator::$_enumTypes))
			{
				preg_match("/\(([^)]+)\)/", $this->columns[$i]['Type'], $enumValues);

				$this->columns[$i]['EnumValuesString'] = $enumValues[1];
				$enumValues = str_replace("'", '', $enumValues[1]);
				$this->columns[$i]['EnumValues'] = explode(',', $enumValues[1]);
			}
		}
	}

	protected function setMaxSizes()
	{
		for($i=0;$i< count($this->columns);$i++){

			if(in_array($this->columns[$i]['ShortType'], array_merge(ModelGenerator::$_stringTypes, ModelGenerator::$_integerTypes)) && strpos($this->columns[$i]['Type'], '(') !== false)
			{
				preg_match("/\(([^)]+)\)/", $this->columns[$i]['Type'], $maxSize);
				$this->columns[$i]['MaxSize'] = $maxSize[1];

			}
		}
	}




}
