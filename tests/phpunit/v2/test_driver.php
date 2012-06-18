<?php

require_once __DIR__ . '/driver.php';

class APIProducerV2DriverTest extends PHPUnit_Framework_TestCase {

	protected $api;

	protected function setUP() {
		$this->api = new TestAPIProducerV2Driver();
	}

	public function testvalidateParameter_sortField() {
		$got = $this->api->validateParameter_sortField('foobar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField1() {
		$got = $this->api->validateParameter_sortField('foobar1');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField2() {
		$got = $this->api->validateParameter_sortField('foo-bar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField3() {
		$got = $this->api->validateParameter_sortField('foo_bar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField4() {
		$got = $this->api->validateParameter_sortField('foo-bar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField5() {
		$got = $this->api->validateParameter_sortField('foo.bar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField6() {
		$got = $this->api->validateParameter_sortField('foo:bar');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField7() {
		$got = $this->api->validateParameter_sortField('foo:bar:blaz');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField8() {
		$got = $this->api->validateParameter_sortField('foo bar');
		$this->assertFalse($got);
	}

	public function testvalidateParameter_sortField9() {
		$got = $this->api->validateParameter_sortField('');
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField10() {
		$got = $this->api->validateParameter_sortField(NULL);
		$this->assertTrue($got);
	}

	public function testvalidateParameter_sortField11() {
		$got = $this->api->validateParameter_sortField(false);
		$this->assertFalse($got);
	}

	public function testvalidateParameter_sortField12() {
		$got = $this->api->validateParameter_sortField(true);
		$this->assertFalse($got);
	}
}

?>
