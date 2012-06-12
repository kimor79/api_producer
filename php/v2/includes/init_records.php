<?php

$api = array();
$drivers = array();

if(!isset($drivers_needed)) {
	$drivers_needed = array();
}

if(!isset($api_loader)) {
	$api_loader = array();
}

if(!array_key_exists('input_file', $api_loader)) {
	$api_loader['input_file'] = 'api_producer/v2/classes/input.php';
}

if(!array_key_exists('input_class', $api_loader)) {
	$api_loader['input_class'] = 'APIProducerV2Input';
}

include $api_loader['input_file'];
$_input_class = $api_loader['input_class'];
$api['input'] = new $_input_class();

if(!array_key_exists('output_file', $api_loader)) {
	$api_loader['output_file'] =
		'api_producer/v2/classes/output_records.php';
}

if(!array_key_exists('output_class', $api_loader)) {
	$api_loader['output_class'] = 'APIProducerV2OutputRecords';
}

include $api_loader['output_file'];
$_output_class = $api_loader['output_class'];
$api['output'] = new $_output_class();

if(!array_key_exists('config_file', $api_loader)) {
	$api_loader['config_file'] =
		'api_producer/v2/classes/config.php';
}

if(!array_key_exists('config_class', $api_loader)) {
	$api_loader['config_class'] = 'APIProducerV2Config';
}

try {
	include $api_loader['config_file'];
	$_config_class = $api_loader['config_class'];
	$api['config'] = new $_config_class();
} catch (Exception $e) {
	$api['output']->sendData(array(), 0, 500, $e->getMessage());
	exit(0);
}

$_authn = $api['config']->getValue('authentication');
if(!is_array($_authn)) {
	$_authn = array();
}

if(!array_key_exists('file', $_authn)) {
	$_authn['file'] = 'api_producer/v2/classes/authentication.php';
}

if(!array_key_exists('class', $_authn)) {
	$_authn['class'] = 'APIProducerV2Authentication';
}

require_once($_authn['file']);
$_authn_class = $_authn['class'];
$api['authn'] = new $_authn_class($_authn);

$_authz = $api['config']->getValue('authorization');
if($_authz) {
	if(!array_key_exists('file', $_authz)) {
		$api['output']->sendData(array(), 0, 500,
			'No authorization file configured');
		exit(0);
	}

	if(!array_key_exists('class', $_authz)) {
		$api['output']->sendData(array(), 0, 500,
			'No authorization class configured');
		exit(0);
	}

	require_once($_authz['file']);
	$_authz_class = $_authz['class'];
	$api['authz'] = new $_authz_class($_authz);
}

while(list($key, $type) = each($drivers_needed)) {
	$driver = 'driver-' . $key;

	$c_driver = $api['config']->getValue($driver);

	if(!is_array($c_driver)) {
		$api['output']->sendData(array(), 0,
			500, $key . ' driver not configured');
		exit(0);
	}

	if(!array_key_exists('file', $c_driver)) {
		$api['output']->sendData(array(), 0,
			500, 'No driver file configured');
		exit(0);
	}

	if(!array_key_exists('class', $c_driver)) {
		$api['output']->sendData(array(), 0,
			500, 'No driver class configured');
		exit(0);
	}

	require_once($c_driver['file']);

	try {
		$slave_okay = false;
		switch($type) {
			case 'ro':
				$slave_okay = true;
				break;
		}

		$driver_class = $c_driver['class'];

		$drivers[$key] = new $driver_class($slave_okay,
			$c_driver);
	} catch (Exception $e) {
		$api['output']->sendData(array(), 0,
			500, $e->getMessage());
		exit(0);
	}
}

?>
