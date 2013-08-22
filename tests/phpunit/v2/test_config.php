<?php

require_once __DIR__ . '/config.php';

define('API_PRODUCER_MYAPP', 'api_producer_phpunit');

$_SERVER['API_PRODUCER_PHPUNIT_CONFIG_FILE'] = __DIR__ . '/config.ini';

class APIProducerV2ConfigTest extends PHPUnit_Framework_TestCase {

	protected $api;

	protected function setUP() {
		$this->api = new TestAPIProducerV2Config();
	}

	public function testGetValue() {
		$got = $this->api->getValue('');
		$this->assertNull($got);
	}

	public function testGetValue1() {
		$got = $this->api->getValue('NOEXIST');
		$this->assertNull($got);
	}

	public function testGetValue2() {
		$got = $this->api->getValue('exists');
		$this->assertSame('yes', $got);
	}

	public function testGetValue3() {
		$got = $this->api->getValue('noexist', 'no');
		$this->assertSame('no', $got);
	}

	public function testGetValue4() {
		$got = $this->api->getValue('Exists');
		$this->assertNull($got);
	}

	public function testGetValue5() {
		$got = $this->api->getValue('exists', 'no');
		$this->assertSame('yes', $got);
	}

	public function testGetValue6() {
		$got = $this->api->getValue('sub1');
		$expect = array('bar' => 'foo');
		$this->assertEquals($expect, $got);
	}

	public function testIsEnabled() {
		$got = $this->api->isEnabled('foo');
		$this->assertTrue($got);
	}

	public function testIsEnabled1() {
		$got = $this->api->isEnabled('bar');
		$this->assertFalse($got);
	}

	public function testIsEnabled2() {
		$got = $this->api->isEnabled('sub1');
		$this->assertFalse($got);
	}

	public function testIsEnabled3() {
		$got = $this->api->isEnabled('sub2');
		$this->assertTrue($got);
	}

	public function testIsEnabled4() {
		$got = $this->api->isEnabled('sub3');
		$this->assertTrue($got);
	}

	public function testIsEnabled5() {
		$got = $this->api->isEnabled('sub4');
		$this->assertFalse($got);
	}
}

?>
