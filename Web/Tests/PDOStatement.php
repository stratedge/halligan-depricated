<?php

class PDOStatement_Stub extends \PDOStatement {

	public static $results = array();
	public static $last_sql = NULL;


	//---------------------------------------------------------------------------------------------


	public function __construct()
	{
		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function addResult($result, $index = NULL)
	{
		if(is_null($index) || !is_numeric($index))
		{
			self::$results[] = $result;
		}
		else
		{
			self::$results[(int) $index] = $result;
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function fetch($how = NULL, $orientation = NULL, $offset = NULL)
	{
		return array_shift(self::$results);
	}


	//---------------------------------------------------------------------------------------------
	

	public function fetchColumn($column_number = NULL)
	{
		if(!isset(self::$results[0])) return FALSE;

		$result = self::$results[0];

		if(count($result))
		{
			$val = array_shift($result);
			self::$results[0] = $result;
			return $val;
		}

		array_shift(self::$results);
		return FALSE;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function getLastSQL()
	{
		return self::$last_sql;
	}


	//---------------------------------------------------------------------------------------------
	

	public static function reset()
	{
		self::$results = array();
		self::$last_sql = NULL;
	}

}

/* End of file PDOStatement.php */
/* Location: ./Web/Tests/PDOStatement.php */