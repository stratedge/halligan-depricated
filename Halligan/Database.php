<?php

namespace Halligan;

use PDO;

class Database {

	protected static $_conns = array();
	protected static $_current;


	//---------------------------------------------------------------------------------------------


	/**
	 * [connect description]
	 * @param	[type]	$conn	[description]
	 * @return	[type]			[description]
	 */
	public static function connect($conn = NULL)
	{
		//If no connection is given, try to use the current one, otherwise use the default one
		if(is_null($conn))
		{
			$conn = static::$_current ?: Config::get('Database', 'default_connection');
		}
		else
		{
			$conn = !empty($conn) ? $conn : Config::get('Database', 'default_connection');
		}

		//If we have already opened this connection, use the pre-existing one
		if(isset(static::$_conns[$conn])) return static::$_conns[$conn];

		//Need to establish the connection
		$conn_params = static::_buildConnectionParams($conn);

		static::$_conns[$conn] = new PDO($conn_params['host'], $conn_params['username'], $conn_params['password']);

		static::$_current = $conn;

		return static::$_conns[$conn];
	}


	//---------------------------------------------------------------------------------------------
	

	/**
	 * [_buildConnectionParams description]
	 * @param	[type]	$conn	[description]
	 * @return	[type]			[description]
	 */
	protected static function _buildConnectionParams($conn)
	{
		$params = Config::get('Database', "connections.$conn");

		$host = isset($params['host']) && !empty($params['host']) ? sprintf("host=%s;", $params['host']) : NULL;
		$dbname = isset($params['database']) && !empty($params['database']) ? sprintf("dbname=%s;", $params['database']) : NULL;
		$port = isset($params['port']) && !empty($params['port']) ? sprintf("port=%s;", $params['port']) : NULL;

		return array(
			'host' => "mysql:" . $host . $dbname . $port,
			'username' => $params['username'],
			'password' => $params['password']
		);
	}
	

	//---------------------------------------------------------------------------------------------


	/**
	 * [query description]
	 * @param	[type]	$sql	[description]
	 * @param	[type]	$query	[description]
	 * @return	[type]			[description]
	 */
	public function query($sql)
	{
		$result = $this->connect()->query($sql);

		//Something failed, find out what
		if($result === FALSE) throw new DatabaseException($this->getError(), 1);

		return new QueryResult($result, $this->connect()->lastInsertId());
	}


	//---------------------------------------------------------------------------------------------
	

	public function getError()
	{
		return $this->connect()->errorInfo();
	}

}

/* End of file Database.php */
/* Location: ./Halligan/Database.php */