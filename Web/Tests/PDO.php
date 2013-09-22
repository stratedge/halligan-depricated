<?php

class PDO_Stub extends \PDO {

	public static $pdo = NULL;

	function __construct($a, $b, $c)
	{
		if(is_null(self::$pdo)) self::$pdo = $this;

		return self::$pdo;
	}

	function quote($sql, $paramtype = NULL)
	{
		return "'" . $sql . "'";
	}
}