<?php

require_once 'api_producer/v2/classes/output.php' ;

class TestAPIProducerV2Output extends APIProducerV2Output {

	public function __construct() {
		parent::__construct();
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	public function formatData_json() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'formatData_json'),
			$args);
	}

	public function formatData_print_r() {
		$args = func_get_args();
		return call_user_func_array(
			array('parent', 'formatData_print_r'),
			$args);
	}

	public function getParameter() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'getParameter'),
			$args);
	}

	public function getParameters() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'getParameters'),
			$args);
	}

	public function setParameters() {
		$args = func_get_args();
		return call_user_func_array(array('parent', 'setParameters'),
			$args);
	}
}

?>
