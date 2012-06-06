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

	public function testSanitizeInput_fqdn() {
		$got = $this->api->sanitizeInput_fqdn('foobar');
		$this->assertSame('foobar', $got);
	}

	public function testSanitizeInput_fqdn1() {
		$got = $this->api->sanitizeInput_fqdn('foobar1');
		$this->assertSame('foobar1', $got);
	}

	public function testSanitizeInput_fqdn2() {
		$got = $this->api->sanitizeInput_fqdn('192.168.6.1');
		$this->assertSame('192.168.6.1', $got);
	}

	public function testSanitizeInput_fqdn3() {
		$got = $this->api->sanitizeInput_fqdn('www.foobar.com.');
		$this->assertSame('www.foobar.com', $got);
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

	public function testValidateInput_fqdn() {
		$got = $this->api->validateInput_fqdn('1337970727');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn1() {
		$got = $this->api->validateInput_fqdn('fe80::a00:27ff:fe03:29cd/64');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn2() {
		$got = $this->api->validateInput_fqdn('192.168.6.1');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn3() {
		$got = $this->api->validateInput_fqdn('192.168.6.1000');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn4() {
		$got = $this->api->validateInput_fqdn('192.168.6.a');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn5() {
		$got = $this->api->validateInput_fqdn('www.foobar.com');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn6() {
		$got = $this->api->validateInput_fqdn('foobar');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn7() {
		$got = $this->api->validateInput_fqdn('foobar.');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn8() {
		$got = $this->api->validateInput_fqdn('fooBar.');
		$this->assertTrue($got);
	}

	public function testValidateInput_fqdn9() {
		$got = $this->api->validateInput_fqdn('.foobar.');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn10() {
		$got = $this->api->validateInput_fqdn('foobar..');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn11() {
		$got = $this->api->validateInput_fqdn('..foobar..');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn12() {
		$got = $this->api->validateInput_fqdn('fe80::a00:27ff:fe03:29cd');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn13() {
		$got = $this->api->validateInput_fqdn('foobar/a');
		$this->assertFalse($got);
	}

	public function testValidateInput_fqdn14() {
		$got = $this->api->validateInput_fqdn('foobar:a');
		$this->assertFalse($got);
	}
}

?>
