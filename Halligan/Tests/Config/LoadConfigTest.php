<?php

namespace Halligan\Tests\Config;

require_once realpath(__DIR__ . "/../config.php");

class LoadConfigTest extends HalliganTestCase {


	protected function setUp()
	{
		Config::$configs_loaded = array();
		Config::$config_options = array();
	}


	//---------------------------------------------------------------------------------------------
	

	public function testConfigLoadedReturnsTrue()
	{
		Config::$configs_loaded = array('Test');

		$this->assertTrue(Config::loadConfig('Test'));
	}


	//---------------------------------------------------------------------------------------------
	

	public function testConfigAddedToLoadedList()
	{
		Config::loadConfig('Test');

		$this->assertContains('Test', Config::$configs_loaded);
	}

}

/* End of file LoadConfigTest.php */
/* Location: ./Halligan/Tests/Config/LoadConfigTest.php */