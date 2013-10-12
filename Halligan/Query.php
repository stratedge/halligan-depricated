<?php

namespace Halligan;

class Query {

	protected $_insert_ignore = FALSE;
	protected $_insert_columns = array();
	protected $_insert_values = array();
	protected $_limit = array();
	protected $_on_duplicate_keys = array();
	protected $_orders = array();
	protected $_result;
	protected $_selects = array();
	protected $_sets = array();
	protected $_table;
	protected $_where_conditionals = array("=", "!=", ">", "<", ">=", "<=", "LIKE");
	protected $_where_type = ' AND ';
	protected $_wheres = array();

	protected $_db = NULL;


	//---------------------------------------------------------------------------------------------
	

	public function __construct($db = NULL)
	{
		if(!is_null($db))
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \Database();
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function select($select = '*', $escape = TRUE)
	{
		if(is_array($select))
		{
			foreach($select as $item)
			{
				$this->select($item, $escape);
			}
		}
		elseif(is_string($select))
		{
			$this->_selects[] = $escape ? $this->_addBackticks($select) : $select;
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function where($column, $value = NULL, $cond = NULL)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column))
		{
			foreach((array) $column as $key => $val)
			{
				if(is_array($val))
				{
					$cond = $val[0];
					$val = $val[1];
				}
				
				call_user_func_array(array($this, 'where'), array($key, $val, $cond));
			}
		}
		//If column is an array, then we're trying to add multiple at once
		elseif(is_array($column))
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
		$column = $this->_addBackticks($column);

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
			$this->_orders[] = in_array(strtoupper($dir), array("ASC", "DESC"), TRUE) ? sprintf("%s %s", $column, strtoupper($dir)) : $column;
		}

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function limit($count, $offset = NULL)
	{
		$this->_limit = !is_null($offset) ? array($offset, $count) : array($count);

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function get($table = NULL)
	{
		$select = $this->_buildSelect();
		
		$from = $this->_buildFrom($table);

		$where = $this->_buildWhere();

		$order = $this->_buildOrder();

		$limit = $this->_buildLimit();

		$parts = array_filter(array($select, $from, $where, $order, $limit));

		$sql = implode(" " , $parts) . ";";

		$this->_clear();

		return $this->query($sql);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildSelect()
	{
		if(empty($this->_selects)) $this->_selects = array("*");
		return "SELECT " . implode(", ", $this->_selects);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildFrom($table)
	{
		$this->_table = $table ?: $this->_table;
		return "FROM " . $this->_addBackticks($this->_table);
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
	

	protected function _buildLimit()
	{
		if(empty($this->_limit)) return NULL;

		return "LIMIT " . implode(", ", $this->_limit);
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _addBackticks($item)
	{
		$pieces = explode(".", $item);

		foreach($pieces as &$piece)
		{
			if(!empty($piece) && $piece != "*") $piece = sprintf("`%s`", $piece);
		}

		return implode(".", $pieces);
	}


	//---------------------------------------------------------------------------------------------
	

	public function set($column, $value = NULL, $escape = TRUE)
	{
		if((is_array($column) && is_assoc($column)) || is_object($column))
		{
			foreach((array) $column as $key => $val)
			{
				call_user_func_array(array($this, 'set'), array($key, $val, $escape));
			}
		}
		elseif(is_array($column))
		{
			foreach($column as $set)
			{
				call_user_func_array(array($this, 'set'), (array) $set);
			}
		}
		else
		{
			$this->_sets[] = sprintf("%s = %s", $this->_addBackticks($column), $escape ? $this->escape($value) : $value);
			$this->_insert_columns[] = $this->_addBackticks($column);
			$this->_insert_values[0][] = $escape ? $this->escape($value) : $value;
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

		return $this->query($sql);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildUpdate($table)
	{
		$this->_table = $table ?: $this->_table;
		return "UPDATE " . $this->_table;
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildSet()
	{
		return "SET " . implode(", ", $this->_sets);
	}


	//---------------------------------------------------------------------------------------------


	public function values(Array $values, $escape = TRUE)
	{
		if(!is_assoc($values))
		{
			foreach($values as $value)
			{
				$this->values($value, $escape);
			}
		}
		else
		{
			$this->_insert_columns = array_keys($values);

			if($escape)
			{
				foreach($values as &$value)
				{
					$value = $this->escape($value);
				}
			}

			$this->_insert_values[] = array_values($values);
			
		}
		
		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function insertIgnore()
	{
		$this->_insert_ignore = TRUE;

		return $this;
	}


	//---------------------------------------------------------------------------------------------


	public function insert($table = NULL, Array $data = NULL, $escape = TRUE)
	{
		if(!is_null($data))
		{
			$this->values($data, $escape);
		}

		$sql = $this->_buildInsert($table);

		return $this->query($sql);
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildInsert($table)
	{
		$open = $this->_insert_ignore ? "INSERT IGNORE INTO" : "INSERT INTO";

		$this->_table = $table ?: $this->_table;

		$columns = $this->_buildColumns();

		$values = $this->_buildValues();

		$parts = array_filter(array($open, $this->_table, $columns, "VALUES", $values));

		$sql = implode(" " , $parts);

		$this->_clear();

		return $sql;
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildColumns()
	{
		return sprintf("(%s)", implode(", ", $this->_insert_columns));
	}


	//---------------------------------------------------------------------------------------------


	protected function _buildValues()
	{
		$values = array();

		foreach($this->_insert_values as $value)
		{
			$values[] = implode(",", $value);
		}

		return sprintf("(%s)", implode("),(", $values));
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
		return $this->_db->connect()->quote($value);
	}


	//---------------------------------------------------------------------------------------------


	public function startTransaction()
	{
		$this->_db->connect()->beginTransaction();

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function rollBack()
	{
		$this->_db->connect()->rollBack();

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function commit()
	{
		$this->_db->connect()->commit();

		return $this;
	}


	//---------------------------------------------------------------------------------------------
	

	public function getSchema($table)
	{
		$sql = sprintf("DESCRIBE %s", $this->_addBackticks($table));

		return $this->query($sql);
	}


	//---------------------------------------------------------------------------------------------
	

	public function lastInsertId($name = NULL)
	{
		return $this->_db->connect()->lastInsertId($name);
	}


	//---------------------------------------------------------------------------------------------
	

	public function query($sql)
	{
		return $this->_db->query($sql);
	}


}

/* End of file Query.php */
/* Location: ./Halligan/Query.php */