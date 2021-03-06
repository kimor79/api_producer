<?php

require_once __DIR__ . '/../../../php/v2/classes/config.php';

class TestAPIProducerV2Config extends APIProducerV2Config {

	public function __construct() {
		parent::__construct();
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	public function getValue() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'getValue'),
			$args);
	}

	public function isEnabled() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'isEnabled'),
			$args);
	}
}

?>
