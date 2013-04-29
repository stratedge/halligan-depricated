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
		if(empty($template) || !is_string($template) || is_numeric($template)) return FALSE;

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
		return isset($this->_template) && !empty($this->_template) && is_string($this->_template) && !is_numeric($this->_template);
	}


	//---------------------------------------------------------------------------------------------


	protected function _getTemplateFilePath()
	{
		foreach(get_all_paths_ordered() as $path)
		{
			$path = realpath($path . 'template' . DS . $this->_template . EXT);
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
				if(!isset($matches[2])) return NULL;

				//Allowing dot notation in variable names for arrays
				$matches[2] = $this->_parseDotNotation($matches[2]);

				return sprintf('<?php if(isset(%s)) echo %s; ?>', $matches[2], $matches[2]);
				break;

			case 'var:escape:':
				if(!isset($matches[2])) return NULL;

				//Allowing dot notation in variable names for arrays
				$matches[2] = $this->_parseDotNotation($matches[2]);

				return sprintf('<?php if(isset(%s)) echo addslashes(%s); ?>', $matches[2], $matches[2]);

			case 'if:':
				$condition = preg_replace_callback(
					'/[\"\']*[A-Za-z][A-Za-z0-9\_\[\]\'\"]+[\"\']*/',
					function($matches) {
						return preg_match('/^[\"\'][\w]+[\"\']$/', $matches[0]) ? $matches[0] : $this->_parseDotNotation($matches[0]);
					},
					$matches[2]
				);
				return sprintf('<?php if(%s): ?>', $condition);
				break;

			case '/if':
				return '<?php endif; ?>';
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
	}


	//---------------------------------------------------------------------------------------------


	protected function _compileTemplate($path)
	{
		$compiled_path = $this->_getCompiledTemplatePath($path);

		//Is the template not yet compiled or out of date?
		if(file_exists($compiled_path) === FALSE || filemtime($path) >= filemtime($compiled_path))
		{
			$contents = file_get_contents($path);
			$template = preg_replace_callback('/\{(\/if|if:|var:escape:|var:|\/foreach|foreach:|template:)([^\}]+)*\}/', array($this, '_parseTag'), $contents);
			file_put_contents($compiled_path, $template);
		}

		return $compiled_path;
	}


	//---------------------------------------------------------------------------------------------
	

	protected function _getCompiledTemplatePath($path)
	{
		return path('app') . 'cache/template' . DS . md5($path) . EXT;
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
		//Return the value if there is no dot in it
		if(strpos($var, '.') === FALSE) return sprintf("$%s", $var);

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

}

/* End of file Template.php */
/* Location: ./Halligan/Template.php */