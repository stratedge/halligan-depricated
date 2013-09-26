<?php

namespace Halligan;

use PDO;

class QueryResult {

	protected $_result = NULL;
	protected $_last_insert_id = NULL;


	//---------------------------------------------------------------------------------------------
	

	public function __construct($result = NULL, $last_insert_id = NULL)
	{
		$this->setResult($result);
		$this->setLastInsertId($last_insert_id);

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function setResult($result)
	{
		$this->_result = $result;
	}


	//---------------------------------------------------------------------------------------------
	

	public function setLastInsertId($last_insert_id)
	{
		$this->_last_insert_id = $last_insert_id;
	}


	//---------------------------------------------------------------------------------------------
	

	public function getLastInsertId()
	{
		return $this->_last_insert_id;
	}


	//---------------------------------------------------------------------------------------------
	

	public function hasResult()
	{
		return !is_null($this->_result);
	}


	//---------------------------------------------------------------------------------------------
	

	public function resultIsPDO()
	{
		return ($this->hasResult() && is_a($this->_result, 'PDOStatement'));
	}


	//---------------------------------------------------------------------------------------------
	

	public function getRow($style = 'object')
	{
		if($this->resultIsPDO())
		{
			$style = $this->_getPDOFetchStyle($style);

			return $this->_result->fetch($style);
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function getOne()
	{
		if($this->resultIsPDO())
		{
			$style = $this->_getPDOFetchStyle('array');

			$row = $this->_result->fetch($style);

			$row = array_values($row);

			return isset($row[0]) ? $row[0] : NULL;
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function getAll($style = 'object')
	{
		if($this->resultIsPDO())
		{
			$style = $this->_getPDOFetchStyle($style);

			$data = array();

			while($item = $this->_result->fetch($style)) $data[] = $item;

			return $data;
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function getOneColumn($index = 0)
	{
		if($this->resultIsPDO())
		{
			return $this->_result->fetchColumn($index);
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function getAllColumn($index = 0)
	{
		if($this->resultIsPDO())
		{
			$data = array();

			while($item = $this->_result->fetchColumn($index)) $data[] = $item;

			return $data;
		}
	}


	//---------------------------------------------------------------------------------------------
	

	public function getNumRows()
	{
		if($this->resultIsPDO())
		{
			return $this->_result->rowCount();
		}
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _getPDOFetchStyle($style)
	{
		switch($style)
		{
			case 'object':
			case 'obj':
				return PDO::FETCH_OBJ;
				break;

			case 'array':
				return PDO::FETCH_ASSOC;
				break;
		}

		return NULL;
	}
}

/* End of file QueryResult.php */
/* Location: ./Halligan/QueryResult.php */