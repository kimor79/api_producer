<?php

require_once __DIR__ . '/validators.php';

class APIProducerV2ValidatorsTest extends PHPUnit_Framework_TestCase {

	protected $api;

	protected function setUP() {
		$this->api = new TestAPIProducerV2Validators();
	}

	public function testSanitizeInput_bool_false() {
		$got = $this->api->sanitizeInput_bool_false(0);
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false1() {
		$got = $this->api->sanitizeInput_bool_false('0');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false2() {
		$got = $this->api->sanitizeInput_bool_false('');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_false3() {
		$got = $this->api->sanitizeInput_bool_false(1);
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_false4() {
		$got = $this->api->sanitizeInput_bool_false('1');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_false5() {
		$got = $this->api->sanitizeInput_bool_false('foobar');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true() {
		$got = $this->api->sanitizeInput_bool_true(0);
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true1() {
		$got = $this->api->sanitizeInput_bool_true('0');
		$this->assertFalse($got);
	}

	public function testSanitizeInput_bool_true2() {
		$got = $this->api->sanitizeInput_bool_true('');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true3() {
		$got = $this->api->sanitizeInput_bool_true(1);
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true4() {
		$got = $this->api->sanitizeInput_bool_true('1');
		$this->assertTrue($got);
	}

	public function testSanitizeInput_bool_true5() {
		$got = $this->api->sanitizeInput_bool_true('foobar');
		$this->assertTrue($got);
	}

/*
	public function testSanitizeInput_dollar() {
		$got = $this->api->sanitizeInput_dollar('1000.00');
		$this->assertSame('1000.00', $got);
	}

	public function testSanitizeInput_dollar1() {
		$got = $this->api->sanitizeInput_dollar('1000');
		$this->assertSame('1000.00', $got);
	}

	public function testSanitizeInput_dollar2() {
		$got = $this->api->sanitizeInput_dollar('1234.56');
		$this->assertSame('1234.56', $got);
	}

	public function testSanitizeInput_dollar3() {
		$got = $this->api->sanitizeInput_dollar('01234.56');
		$this->assertSame('1234.56', $got);
	}
*/

	public function testSanitizeInput_timestamp() {
		$now = time();
		$got = $this->api->sanitizeInput_timestamp($now);
		$this->assertSame($now, $got);
	}

	public function testSanitizeInput_timestamp1() {
		$got = $this->api->sanitizeInput_timestamp(
			'Fri, 25 May 2012 18:32:07 GMT');
		$this->assertSame(1337970727, $got);
	}

	public function testSanitizeInput_timestamp2() {
		$got = $this->api->sanitizeInput_timestamp(1337970727);
		$this->assertSame(1337970727, $got);
	}

	public function testSanitizeInput_timestamp3() {
		$got = $this->api->sanitizeInput_timestamp('1337970727');
		$this->assertSame(1337970727, $got);
	}
}

?>
