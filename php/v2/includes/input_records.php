<?php

if(!is_array($defaults)) {
	$defaults = array();
}

if(!is_array($errors)) {
	$errors = array();
}

if(!is_array($input)) {
	$input = array();
}

if(!is_array($optional)) {
	$optional = array();
}

if(!is_array($params)) {
	$params = array();
}

if(!is_array($required)) {
	$required = array();
}

if(!is_array($sanitize)) {
	$sanitize = array();
}

if($isset($inputfrom)) {
	$inputfrom = 'GJP';
}

if(!isset($extrainput)) {
	$extrainput = true;
}

list($input, $params) = $api['input']->getInput(array('input' => $inputfrom));

$output_params = array_intersect_key($params, $api['output']->getParameters());
$errors = $api['output']->setParameters($output_params);
if($errors) {
	$api['output']->sendData(array(), 0, 400, implode("\n", $errors));
	exit(0);
}

if(!$api['authn']->isAuthenticated()) {
	$api['output']->sendData(array(), 0, 401, 'Not authenticated');
	exit(0);
}

$input = $api['input']->removeValues($input);
$input = array_merge($defaults, $input);
$input = $api['input']->gpcSlashInput($input);

$errors = $api['input']->validateInput($input, $required, $optional,
	$extrainput);
if($errors) {
	$api['output']->sendData(array(), 0, 400, implode("\n", $errors));
	exit(0);
}

$input = $api['input']->sanitizeInput($input, $sanitize);

?>
