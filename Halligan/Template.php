<?php

namespace Halligan;

class Template {

	protected $_template = NULL;
	protected $_data = array();


	//---------------------------------------------------------------------------------------------


	public function __construct($template = NULL, Array $data = array())
	{
		$this->setTemplate($template);
		$this->addData($data);
	}


	//---------------------------------------------------------------------------------------------


	public function setTemplate($template)
	{
		if(empty($template) || !is_string($template)) return FALSE;

		$this->_template = $template;
	}


	//---------------------------------------------------------------------------------------------


	public function addData($map, $value = NULL)
	{
		//If the map is an associative array, merge into our data array
		if(is_assoc($map) || is_object($map))
		{
			return ($this->_data = array_merge($this->_data, $this->_recursiveConvertObjectToArray($map)));
		}

		//Is the specified key a valid php variable name?
		if(!is_string($map) || is_numeric($map)) return FALSE;

		//Set the specified key in the data array to the passed value
		return ($this->_data[$map] = $this->_recursiveConvertObjectToArray($value));
	}


	//---------------------------------------------------------------------------------------------


	public function build()
	{
		if(!$this->_hasTemplate()) return FALSE;

		//Try and load the template file
		$path = $this->_getTemplateFilePath();

		//No file found, exit gracefully (for now)
		if($path === FALSE) return FALSE;

		//Get the compiled template's path
		$compiled_template = $this->_compileTemplate($path);

		//Start output buffermg
		ob_start();

		//Before we include the compiled template, make the data availabe as PHP variables
		extract($this->_data);

		//Include the template file
		include($compiled_template);
		
		//Return the buffered output
		return ob_get_clean();
	}


	//---------------------------------------------------------------------------------------------


	protected function _hasTemplate()
	{
		return isset($this->_template) && !empty($this->_template) && is_string($this->_template);
	}


	//---------------------------------------------------------------------------------------------


	protected function _getTemplateFilePath()
	{
		foreach(get_all_paths_ordered() as $path)
		{
			$path = realpath($path . 'Template' . DS . $this->_template . EXT);
			if($path !== FALSE) break;
		}

		return $path;
	}


	//---------------------------------------------------------------------------------------------


	protected function _parseTag($matches)
	{
		switch($matches[1])
		{
			case 'var:':
			case 'echo:':
			case 'var:escape:':
			case 'echo:escape:':
				if(!isset($matches[2])) return NULL;
				return $this->_parseEchoTag($matches[2], strpos($matches[1], ":escape") !== FALSE);
				break;

			case 'if:':
				if(!isset($matches[2])) return NULL;
				$condition = $this->_parseIfCondition($matches[2]);
				return sprintf('<?php if(%s): ?>', $condition);
				break;

			case '/if':
				return '<?php endif; ?>';
				break;

			case 'if:var:':
			case 'if:echo:':
			case 'if:var:escape:':
			case 'if:echo:escape:':
				if(!isset($matches[2])) return NULL;
				return $this->_parseEchoIf($matches[2], strpos($matches[1], ":escape") !== FALSE);
				break;

			case 'foreach:':
				$parts = explode(":", $matches[2]);
				if(count($parts) < 3) return sprintf('<?php foreach(%s as $%s): ?>', $this->_parseDotNotation($parts[0]), $parts[1]);
				if(count($parts) >= 3) return sprintf('<?php foreach(%s as $%s => $%s): ?>', $this->_parseDotNotation($parts[0]), $parts[1], $parts[2]);
				break;

			case '/foreach':
				return '<?php endforeach; ?>';
				break;

			case 'template:':
				return sprintf('<?php $this->_parseTemplate("%s"); ?>', $matches[2]);
				break;
		}

		return NULL;
	}


	//---------------------------------------------------------------------------------------------


	protected function _compileTemplate($path)
	{
		$compiled_path = $this->_getCompiledTemplatePath($path);

		//Is the template not yet compiled or out of date?
		if(file_exists($compiled_path) === FALSE || filemtime($path) >= filemtime($compiled_path))
		{
			$contents = file_get_contents($path);
			$template = $this->parseTags($contents);
			file_put_contents($compiled_path, $template);
		}

		return $compiled_path;
	}


	//---------------------------------------------------------------------------------------------
	

	public function parseTags($content)
	{
		return preg_replace_callback('/\{(\/if|if:echo:escape:|if:echo:|if:var:escape:|if:var:|if:|var:escape:|var:|echo:escape:|echo:|\/foreach|foreach:|template:)([^\}]+)*\}/', "self::_parseTag", $content);
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _getCompiledTemplatePath($path)
	{
		return path('app') . 'Cache/template' . DS . md5($path) . EXT;
	}


	//---------------------------------------------------------------------------------------------


	protected function _parseTemplate($template)
	{
		//Shallow attempt to block accidental infitine loop
		if($template == $this->_template) return FALSE;

		//Instantiate a new template to render the given template
		$tpl = new Template();
		$tpl->setTemplate($template);

		//The child template should get all the same data this template has
		$tpl->addData($this->_data);

		//Echo out building the template
		echo $tpl->build();
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _parseDotNotation($var)
	{
		//Return the value if there is no dot in it, with a $ if it is a valid variable name
		if(strpos($var, '.') === FALSE) return preg_match('/^[\"\'][^\"\']+[\"\']$/', $var) === 0 ? sprintf("$%s", $var) : $var;

		//Construct an array syntax, casting the variable to an array in case its an object
		$parts = explode(".", $var);
		return sprintf("$%s['%s']", $parts[0], implode("']['", array_slice($parts, 1)));
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _recursiveConvertObjectToArray($value)
	{
		if(is_object($value)) $value = (array) $value;
		
		if(is_array($value))
		{
			foreach($value as $key => &$val)
			{
				if(is_object($val)) $val = $this->_recursiveConvertObjectToArray((array) $val);
			}
		}

		return $value;
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _parseIfCondition($match)
	{
		return preg_replace_callback(
			'/[\"\']*[A-Za-z][A-Za-z0-9\_\[\]\'\"]+[\"\']*/',
			"self::_parseIfVariable",
			$match
		);
	}


	//---------------------------------------------------------------------------------------------


	protected function _parseIfVariable($matches)
	{
		return preg_match('/^[\"\'][\w]+[\"\']$/', $matches[0]) ? $matches[0] : $this->_parseDotNotation($matches[0]);
	}


	//---------------------------------------------------------------------------------------------


	protected function _validVariableName($var)
	{
		return preg_match('/^[\"\'].+[\"\']$/', $var) === 0;
	}


	//---------------------------------------------------------------------------------------------


	protected function _parseEchoIf($body, $escape)
	{
		$parts = explode(":", $body);
		
		//Make sure we have at least a condition
		if(!$parts[0]) return NULL;

		//Parse the condition statement
		$condition = $this->_parseIfCondition($parts[0]);

		//Parse the main echo
		$var = $this->_parseEchoTag($parts[1], $escape, FALSE);

		//If we have an alternative echo, parse it
		$alt = NULL;

		if(isset($parts[2]))
		{
			$alt = sprintf(" else { %s }", $this->_parseEchoTag($parts[2], $escape, FALSE));
		}

		return sprintf("<?php if(%s) { %s }%s ?>", $condition, $var, $alt);
	}


	//---------------------------------------------------------------------------------------------


	protected function _parseEchoTag($var, $escape = FALSE, $incl_php = TRUE)
	{
		//Check if this is a variable or straight text
		$valid = $this->_validVariableName($var);

		if($valid) $var = $this->_parseDotNotation($var);
		 
		//If this is a valid variable, let's add the isset
		$pre = $valid ? sprintf("if(isset(%s)) ", $var) : NULL;

		$post = $escape ? sprintf("addslashes(%s)", $var) : $var;

		$tag = sprintf("%secho %s;", $pre, $post);

		return $incl_php ? sprintf("<?php %s ?>", $tag) : $tag;
	}

}

/* End of file Template.php */
/* Location: ./Halligan/Template.php */