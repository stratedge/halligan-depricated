<?php

namespace Halligan;

use PDO;

class Query extends \Halligan\Database {

	protected $_orders = array();
	protected $_result;
	protected $_selects = array('*');
	protected $_sets = array();
	protected $_table;
	protected $_where_conditionals = array("=", "!=", "LIKE");
	protected $_where_type = ' AND ';
	protected $_wheres = array();


	//---------------------------------------------------------------------------------------------
	

	public function __construct()
	{
		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function select($select = '*')
	{
		if(is_string($select) || (is_array($select) && !is_assoc($select)) || (is_object($select) && !is_assoc((array) $select))) $this->_selects = (array) $select;

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function where($column, $value = NULL, $cond = NULL)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column)) return $this;

		//If column is an array, then we're trying to add multiple at once
		if(is_array($column))
		{
			foreach($column as $where)
			{
				call_user_func_array(array($this, 'where'), $where);
			}
		}
		else
		{
			$this->_addWhere($column, $value, $cond);
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function whereOr($column, $value = NULL, $cond = NULL)
	{
		$this->_where_type = ' OR ';

		return $this->where($column, $value, $cond);
	}


	//---------------------------------------------------------------------------------------------


	public function whereIn($column, $list = NULL)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column)) return $this;

		if(is_array($column))
		{
			foreach($column as $where)
			{
				call_user_func_array(array($this, 'whereIn'), $where);
			}
		}
		else
		{
			if(is_string($list))
			{
				$list = explode(',', $list);
			}

			foreach($list as &$item)
			{
				$item = $this->escape(trim($item));
			}

			$this->_addWhere($column, $list, 'IN');
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	protected function _addWhere($column, $value, $cond)
	{
		//if(is_null($value)) return $this->_wheres[] = $this->escape($column);
		switch($cond)
		{
			case 'IN':
				return $this->_wheres[] =  sprintf("%s %s (%s)", $column, $cond, implode(",", $value));
				break;

			default:
				return $this->_wheres[] = sprintf("%s %s %s", $column, in_array(strtoupper($cond), $this->_where_conditionals) ? $cond : "=", $this->escape($value));
				break;
		}
	}


	//---------------------------------------------------------------------------------------------


	public function table($table)
	{
		if(is_string($table)) $this->_table = $table;

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function order($column, $dir = NULL)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column)) return $this;

		if(is_array($column))
		{
			foreach($column as $order)
			{
				call_user_func_array(array($this, 'order'), (array) $order);
			}
		}
		else
		{
			$this->_orders[] = sprintf("%s %s", $column, $dir == 'DESC' ? 'DESC' : 'ASC');
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function get($table = NULL)
	{
		$select = $this->_buildSelect();
		
		$from = $this->_buildFrom($table);

		$where = $this->_buildWhere();

		$order = $this->_buildOrder();

		$parts = array_filter(array($select, $from, $where, $order));

		$sql = implode(" " , $parts) . ";";

		$this->_clear();

		return $this->query($sql, $this);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildSelect()
	{
		return "SELECT " . implode(", ", $this->_selects);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildFrom($table)
	{
		$this->_table = $table ?: $this->_table;
		return "FROM " . $this->_table;
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildWhere()
	{
		if(empty($this->_wheres)) return NULL;

		return "WHERE " . implode($this->_where_type, $this->_wheres);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildOrder()
	{
		if(empty($this->_orders)) return NULL;

		return "ORDER BY " . implode(", ",  $this->_orders);
	}


	//---------------------------------------------------------------------------------------------
	

	public function set($column, $value = NULL)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column)) return $this;

		if(is_array($column))
		{
			foreach($column as $set)
			{
				call_user_func_array(array($this, 'set'), (array) $set);
			}
		}
		elseif(!is_null($value))
		{
			$this->_sets[] = sprintf("%s = %s", $column, $this->escape($value));
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function update($table = NULL)
	{
		$start = $this->_buildUpdate($table);

		$set = $this->_buildSet();

		$where = $this->_buildWhere();

		$parts = array_filter(array($start, $set, $where));

		$sql = implode(" " , $parts) . ";";

		$this->_clear();

		return $this->query($sql, $this);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildUpdate($table)
	{
		$this->_table = $table ?: $this->_table;
		return "UPDATE " . $this->_table;
	}

	protected function _buildSet()
	{
		return "SET " . implode(", ", $this->_sets);
	}


	//---------------------------------------------------------------------------------------------


	protected function _clear()
	{
		$this->_selects = array();
		$this->_table = NULL;
		$this->_wheres = array();
		$this->_sets = array();
	}


	//---------------------------------------------------------------------------------------------


	public function escape($value)
	{
		return $this->connect()->quote($value);
	}


	//---------------------------------------------------------------------------------------------


	public function hasResult()
	{
		return !empty($this->_result) && is_object($this->_result) && is_a($this->_result, 'PDOStatement');
	}


	//---------------------------------------------------------------------------------------------
	

	public function getArray($table = NULL)
	{
		return $this->_buildResultArray(PDO::FETCH_ASSOC, $table);
	}


	//---------------------------------------------------------------------------------------------
	

	public function getObj($table = NULL)
	{
		return $this->_buildResultArray(PDO::FETCH_OBJ, $table);
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _buildResultArray($type, $table)
	{
		if(!$this->hasResult() || !is_null($table)) $this->get($table);

		//PDO returns FALSE if the query errors
		if($this->_result === FALSE) dump($this->getError());

		$data = array();

		while($item = $this->_result->fetch($type)) $data[] = $item;

		return $data;
	}


	//---------------------------------------------------------------------------------------------
	

	public function getFirstArray()
	{
		return $this->_buildGetFirst(PDO::FETCH_ASSOC);
	}


	//---------------------------------------------------------------------------------------------


	public function getFirstObj()
	{
		return $this->_buildGetFirst(PDO::FETCH_OBJ);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildGetFirst($type)
	{
		if(!$this->hasResult()) $this->get();

		return $this->_result->fetch($type);
	}


	//---------------------------------------------------------------------------------------------
	

	public function numRows()
	{
		if(!$this->hasResult()) $this->get();

		return $this->_result->rowCount();
	}


	//---------------------------------------------------------------------------------------------
	

	public function startTransaction()
	{
		$this->connect()->beginTransaction();

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function rollBack()
	{
		$this->connect()->rollBack();

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function commit()
	{
		$this->connect()->commit();

		return $this;
	}

}

/* End of file Query.php */
/* Location: ./Halligan/Query.php */