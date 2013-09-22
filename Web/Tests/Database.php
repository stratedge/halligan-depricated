<?php

class Database {

	static $results = array();

	public static function connect()
	{
		return new PDO_Stub('test', 'test', 'test');
	}

	public static function query($sql)
	{
		PDOStatement_Stub::$last_sql = $sql;

		$pdo_statement = new PDOStatement_Stub();

		return new QueryResult($pdo_statement);
	}
}