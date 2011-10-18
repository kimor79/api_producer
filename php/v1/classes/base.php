<?php

/**

Copyright (c) 2010, Kimo Rosenbaum and contributors
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the owner nor the names of its contributors
      may be used to endorse or promote products derived from this
      software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

**/

/**
 * ApiProducer
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package ApiProducerBase
 */

class ApiProducerBase {

	protected $api_parameters = array(
		'contentType' => true,
		'flatOutput' => false,
		'outputFormat' => 'json',
		'subDetails' => false,
	);

	// Add content-disposition header
	// Value is file extension
	// Set to NULL to skip header
	protected $content_disposition = array();

	protected $content_types = array(
		'json' => 'application/json',
	);

	protected $output_formats = array(
		'json',
		'print_r',
	);

	protected $parameters = array();

	protected $variables = array(
		'flat_outputs' => array(),
		'multi_separator' => ',',
	);

	public function __construct() {
	}

	public function __deconstruct() {
	}

	/**
	 * Build a query (array(key => array(eq => array() ...
	 * @param array $input
	 * @param array $fields
	 * @return array
	 */
	public function buildQuery($input, $fields) {
		$types = array('ge', 'gt', 'le', 'lt');
		$output = array();

		while(list($junk, $field) = each($fields)) {
			$output[$field] = array();

			if(array_key_exists($field, $input)) {
				$output[$field]['eq'] = (array) $input[$field];
			}

			if(array_key_exists($field . '_re', $input)) {
				$output[$field]['re'] =
					(array) $input[$field . '_re'];
			}

			foreach($types as $type) {
				$key = sprintf("%s_%s", $field, $type);

				if(array_key_exists($key, $input)) {
					if(is_array($input[$key])) {
						$output[$field][$type] =
							$input[$key][0];
					} else {
						$output[$field][$type] =
							$input[$key];
					}
				}
			}

			if(empty($output[$field])) {
				unset($output[$field]);
			}
		}

		return $output;
	}

	/**
	 * Cast input
	 * @param array $input
	 * @param array $cast
	 * @return array
	 */
	public function castInput($input = array(), $cast = array()) {
		$output = array_diff_key($input, $cast);

		$keys = array_intersect_key($cast, $input);

		foreach($keys as $key => $func) {
			$multi = false;
			$tests = array();

			if(substr($func, 0, 7) === '_multi_') {
				$func = substr($func, 7);
				$multi = true;
			}

			if(is_array($input[$key])) {
				$tests = $input[$key];
			} else {
				if($multi) {
					$sep = $this->getVariable(
						'multi_separator');
					$tests = explode($sep, $input[$key]);
				} else {
					$tests[] = $input[$key];
				}
			}

			foreach($tests as $test) {
				if(empty($func)) {
					$value = $test;
				} else {
					$function = 'castInput_' . $func;
					if(!method_exists($this, $function)) {
						continue;
					}

					$value = $this->$function($test);
				}

				if($multi) {
					if(!is_array($output[$key])) {
						$output[$key] = array();
					}

					$output[$key][] = $value;
				} else {
					$output[$key] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Cast to array
	 * @param string $input
	 * @return array
	 */
	protected function castInput_array($input) {
		return (array) $input;
	}

	/**
	 * Cast to binary
	 * @param string $input
	 * @return binary
	 */
	protected function castInput_bin($input) {
		return $this->castInput_binary($input);
	}

	/**
	 * Cast to binary
	 * @param string $input
	 * @return binary
	 */
	protected function castInput_binary($input) {
		return (binary) $input;
	}

	/**
	 * Cast to boolean
	 * @param string $input
	 * @return bool
	 */
	protected function castInput_bool($input) {
		return $this->castInput_boolean($input);
	}

	/**
	 * Cast to boolean
	 * @param string $input
	 * @return bool
	 */
	protected function castInput_boolean($input) {
		return (boolean) $input;
	}

	/**
	 * Cast to double
	 * @param string $input
	 * @return float
	 */
	protected function castInput_double($input) {
		return $this->castInput_float($input);
	}

	/**
	 * Cast to float
	 * @param string $input
	 * @return float
	 */
	protected function castInput_float($input) {
		return (float) $input;
	}

	/**
	 * Cast to int
	 * @param string $input
	 * @return int
	 */
	protected function castInput_int($input) {
		return $this->castInput_integer($input);
	}

	/**
	 * Cast to integer
	 * @param string $input
	 * @return int
	 */
	protected function castInput_integer($input) {
		return (integer) $input;
	}

	/**
	 * Cast to object
	 * @param string $input
	 * @return object
	 */
	protected function castInput_object($input) {
		return (object) $input;
	}

	/**
	 * Cast to real
	 * @param string $input
	 * @return float
	 */
	protected function castInput_real($input) {
		return $this->castInput_float($input);
	}

	/**
	 * Cast to string
	 * @param string $input
	 * @return string
	 */
	protected function castInput_string($input) {
		return (string) $input;
	}

	/**
	 * Cast to unset
	 * @param string $input
	 * @return null
	 */
	protected function castInput_null($input) {
		return (unset) $input;
	}

	/**
	 * Add/modify content-disposition header for given output format
	 * @param string output format
	 * @param string file extension header (or NULL)
	 */
	public function contentDisposition($format, $ext) {
		if(in_array($format, $this->output_formats)) {
			if(is_null($ext) || is_scalar($ext)) {
				$this->content_disposition[$format] = $ext;
			}
		}
	}

	/**
	 * Add/modify content-type header for given output format
	 * @param string output format
	 * @param string content-type header (or NULL)
	 */
	public function contentType($format, $header) {
		if(in_array($format, $this->output_formats)) {
			if(is_null($header) || is_scalar($header)) {
				$this->content_types[$format] = $header;
			}
		}
	}

	/**
	 * Recursively diff two arrays
	 * @param array $old
	 * @param array $new
	 * @return array
	 */
	public function diffArray($old, $new) {
		$diff = array();
		$keys = array_merge(array_keys($old), array_keys($new));

		while(list($junk, $key) = each($keys)) {
			if(!array_key_exists($key, $old)) {
				$diff[$key] = array(
					'old' => NULL,
					'new' => $new[$key],
				);

				continue;
			}

			if(!array_key_exists($key, $new)) {
				$diff[$key] = array(
					'old' => $old[$key],
					'new' => NULL,
				);

				continue;
			}

			if($old[$key] !== $new[$key]) {
				$diff[$key] = array(
					'old' => $old[$key],
					'new' => $new[$key],
				);

				continue;
			}

			if(is_array($old[$key])) {
				$diff[$key] = $this->diffArray($old[$key],
					$new[$key]);

				if(empty($diff[$key])) {
					unset($diff[$key]);
				}

				continue;
			}
		}
		reset($keys);

		return $diff;
	}

	/**
	 * Get the value for given variable item
	 * @param string $item
	 * @return mixed
	 */
	public function getVariable($item) {
		if(array_key_exists($item, $this->variables)) {
			return $this->variables[$item];
		}

		return NULL;
	}

	/**
	 * Get the value for given parameter
	 * @param string $parameter
	 * @return mixed
	 */
	public function getParameter($parameter) {
		if(array_key_exists($parameter, $this->parameters)) {
			return $this->parameters[$parameter];
		}

		return NULL;
	}

	/**
	 * Do the get_magic_quotes_gpc()/stripslashes dance
	 * @param string $input
	 * @return string
	 */
	public function gpcSlash($input) {
		if(get_magic_quotes_gpc()) {
			return stripslashes($input);
		}

		return $input;
	}

	/**
	 * Recursively do the get_magic_quotes_gpc/stripslashes dance
	 * @param array $input
	 * @return array
	 */
	public function gpcSlashInput($input = array()) {
		$output = array();
		while(list($key, $value) = each($input)) {
			if(is_array($value)) {
				$output[$key] = $this->gpcSlashInput($value);
			} else {
				$output[$key] = $this->gpcSlash($value);
			}
		}

		return $output;
	}

	/**
	 * Remove values
	 * @param arary $input
	 * @param array $remove optional list of values (default is '')
	 * @return array
	 */
	public function removeValues($input, $remove = array()) {
		$output = array();

		if(empty($remove)) {
			$remove = array(
				'',
			);
		}

		while(list($key, $value) = each($input)) {
			if(!in_array($value, $remove, true)) {
				$output[$key] = $value;
			}
		}

		return $output;
	}

	/**
	 * Sanitize input
	 * @param array $input
	 * @param array $sanitize
	 * @return array
	 */
	public function sanitizeInput($input = array(), $sanitize = array()) {
		$output = array_diff_key($input, $sanitize);

		$keys = array_intersect_key($sanitize, $input);

		foreach($keys as $key => $func) {
			$multi = false;
			$tests = array();

			if(substr($func, 0, 7) === '_multi_') {
				$func = substr($func, 7);
				$multi = true;
			}

			if(is_array($input[$key])) {
				$tests = $input[$key];
			} else {
				if($multi) {
					$sep = $this->getVariable(
						'multi_separator');
					$tests = explode($sep, $input[$key]);
				} else {
					$tests[] = $input[$key];
				}
			}

			foreach($tests as $test) {
				if(empty($func)) {
					$value = $test;
				} else {
					$function = 'sanitizeInput_' . $func;
					if(!method_exists($this, $function)) {
						continue;
					}

					$value = $this->$function($test);
				}

				if($multi) {
					if(!is_array($output[$key])) {
						$output[$key] = array();
					}

					$output[$key][] = $value;
				} else {
					$output[$key] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Sanitize as bool (defaulting to false)
	 * @param string $input
	 * @return bool
	 */
	protected function sanitizeInput_bool_false($input) {
		return $this->trueFalse($input, false);
	}

	/**
	 * Sanitize as bool (defaulting to true)
	 * @param string $input
	 * @return bool
	 */
	protected function sanitizeInput_bool_true($input) {
		return $this->trueFalse($input, true);
	}

	/**
	 * Sanitize a date (strtotime)
	 * @param string $input
	 * @return int
	 */
	protected function sanitizeInput_date($input) {
		$date = strtotime($input);

		if($date === -1 || $date === false) {
			return 0;
		}

		return $date;
	}

	/**
	 * Sanitize dollar
	 * @param string $input
	 * @return float
	 */
	protected function sanitizeInput_dollar($input) {
		return sprintf("%01.2f", $input);
	}

	/**
	 * Sanitize a fqdn
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_fqdn($input) {
		$input = $this->sanitizeInput_tolower($input);
		$input = rtrim($input, '.');

		return $input;
	}

	/**
	 * gpcSlash it
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_gpcSlash($input) {
		return $this->gpcSlash($input);
	}

	/**
	 * Sanitize a mac address
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_mac_address($input) {
		$input = $this->sanitizeInput_tolower($input);
		$input = str_replace(array('.', ':'), '', $input);

		if(strlen($input) == 14) {
			if(substr($input, 0, 2) === '0x') {
				$input = substr($input, 2);
			}
		}

		return $input;
	}

	/**
	 * Make a string lowercase
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_tolower($input) {
		return strtolower($input);
	}

	/**
	 * Make a string uppercase
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_toupper($input) {
		return strtoupper($input);
	}

	/**
	 * Sanitize contentType value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_contentType($value) {
		return $this->trueFalse($value,
			$this->api_parameters['contentType']);
	}

	/**
	 * Sanitize flatOutput value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_flatOutput($value) {
		return $this->trueFalse($value,
			$this->api_parameters['flatOutput']);
	}

	/**
	 * Sanitize outputFormat value
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_outputFormat($value) {
		$t_value = strtolower((string) $value);
		if(in_array($t_value, $this->output_formats)) {
			return $t_value;
		}

		return $this->api_parameters['outputFormat'];
	}

	/**
	 * Sanitize subDetails value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_subDetails($value) {
		return $this->trueFalse($value,
			$this->api_parameters['subDetails']);
	}

	/**
	 * Send headers (as needed)
	 * @param $string filename (if Content-Disposition is to be sent)
	 */
	public function sendHeaders($filename = NULL) {
		if($this->getParameter('contentType') === false) {
			return;
		}

		$format = $this->getParameter('outputFormat');

		if(array_key_exists($format, $this->content_types)) {
			if(!is_null($this->content_types[$format])) {
				header('Content-Type: ' .
					$this->content_types[$format]);
			}
		}

		if(array_key_exists($format, $this->content_disposition)) {
			if(!is_null($this->content_disposition[$format])) {
				if(is_null($filename)) {
					$filename =substr(strrchr(
						$_SERVER['PHP_SELF'], '/'),
						1) . time();
				}

				$dispo = 'Content-Disposition: attachment;';
				$dispo .= sprintf(" filename=\"%s.%s\"",
					$filename,
					$this->content_disposition[$format]);
				header($dispo);
			}
		}
	}

	/**
	 * Set a variable item
	 * @param string $item
	 * @param mixed $value
	 */
	public function setVariable($item, $value) {
		if(array_key_exists($item, $this->variables)) {
			$this->variables[$item] = $value;
		}
	}

	/**
	 * Remove API parameters from input
	 * @param array $input
	 * @return array
	 */
	public function setInput($input = array()) {
		return array_diff_key($input, $this->parameters);
	}

	/**
	 * Set the API parameters from request (eg $_GET, $_POST)
	 * @param array $input array containing input to look for parameters
	 *	[default = $_GET]
	 * @param array $defaults Override default values
	 */
	public function setParameters($input = false, $defaults = array()) {
		$array = $_GET;

		if(is_array($input)) {
			$array = $input;
		}

		foreach($this->api_parameters as $param => $d_value) {
			$value = $d_value;
			if(array_key_exists($param, $array)) {
				$value = $array[$param];
			} else {
				if(array_key_exists($param, $defaults)) {
					$value = $defaults[$param];
				}
			}

			$function = 'sanitizeParameter_' . $param;
			if(method_exists($this, $function)) {
				$this->parameters[$param] =
					$this->$function($value);
			} else {
				$this->parameters[$param] = $value;
			}
		}

		$flat_outputs = $this->getVariable('flat_outputs');
		if(!empty($flat_outputs[$this->parameters['outputFormat']])) {
			$this->parameters['flatOutput'] = true;
		}
	}

	/**
	 * Show json output
	 * @param array $data
	 */
	protected function showOutput_json($data) {
		echo json_encode($data);
	}

	/**
	 * Show print_r output
	 * @param array $data
	 */
	protected function showOutput_print_r($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}

	/**
	 * Checks whether input is "true" (yes, 1, on) or "false" (no, 0, off)
	 * @param string $input
	 * @param bool $default What to return if input is neither true/false
	 * @return bool
	 */
	public function trueFalse($input, $default) {
		if($input === true) {
			return true;
		}

		if($input === false) {
			return false;
		}

		switch(strtolower((string) $input)) {
			case '1':
			case 'on':
			case 'true':
			case 'yes':
				return true;
			case '0':
			case 'false':
			case 'no':
			case 'off':
				return false;
		}

		if(is_bool($default)) {
			return $default;
		}

		return false;
	}

	/**
	 * Validate input
	 * @param array $input
	 * @param array $required Required keys
	 * @param array $optional Optional keys
	 * @param bool $needed false to skip check for extraneous keys
	 * @return array list of errors or an empty array
	 */
	public function validateInput($input = array(), $required = array(),
			$optional = array(), $needed = false) {
		$errors = array();
		$keys = array();

		foreach($required as $key => $func) {
			if(!array_key_exists($key, $input)) {
				$errors[] = 'Missing ' . $key;
				continue;
			}

			$keys[$key] = $func;
		}

		foreach($optional as $key => $func) {
			if(array_key_exists($key, $input)) {
				$keys[$key] = $func;
			}
		}

		if($needed) {
			$extra = array_diff_key($input, $required, $optional);

			foreach($extra as $key => $junk) {
				$errors[] = 'Extraneous ' . $key;
			}
		}

		foreach($keys as $key => $func) {
			$multi = false;
			$tests = array();

			if(substr($func, 0, 7) === '_multi_') {
				$func = substr($func, 7);
				$multi = true;
			}

			if(is_array($input[$key])) {
				if(!$multi) {
					$errors[] = sprintf(
						"Multiple %s not allowed",
						$key);
					continue;
				}

				$tests = $input[$key];
			} else {
				if($multi) {
					$sep = $this->getVariable(
						'multi_separator');
					$tests = explode($sep, $input[$key]);
				} else {
					$tests[] = $input[$key];
				}
			}

			if(empty($func)) {
				continue;
			}

			$function = 'validateInput_' . $func;
			if(!method_exists($this, $function)) {
				$errors[] = 'Unable to validate ' . $key;
				continue;
			}

			foreach($tests as $test) {
				if($this->$function($test) !== true) {
					$errors[] = 'Invalid ' . $key;
					continue;
				}
			}
		}

		return $errors;
	}

	/**
	 * Validate input is "bool"-like
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_bool($input) {
		if($input === true) {
			return true;
		}

		if($input === false) {
			return true;
		}

		switch(strtolower((string) $input)) {
			case '0':
			case '1':
			case 'false':
			case 'no':
			case 'off':
			case 'on':
			case 'true':
			case 'yes':
				return true;
		}

		return false;
	}

	/**
	 * Validate input is a date (anything strtotime can convert)
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_date($input) {
		$date = strtotime($input);

		if($date !== -1 && $date !== false) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input is a digit
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_digit($input) {
		if(ctype_digit((string) $input)) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input is a dollar amount
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_dollar($input) {
		if(ctype_digit((string) $input)) {
			return true;
		}

		if(preg_match('/^\d+\.\d\d$/', $input)) {
			return true;
		}

		return false;
	}

	/**
	 * Validate input looks like a fully qualified domain name
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_fqdn($input) {
		if(strpos($input, '..') === false) {
			if(preg_match('/^[a-z0-9][a-z0-9.-]+$/i', $input)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate input is a mac address (several forms)
	 * @param string $input
	 * @return bool
	 */
	protected function validateInput_mac_address($input) {
		if(preg_match('/^(?:0x)?[0-9a-z]{12}$/i', $input)) {
			// 0xc82a1403a7fb - snmp output
			// c82a1403a7fb
			return true;
		}

		if(preg_match('/^([0-9a-z]{2}:){5}[0-9a-z]{2}$/i', $input)) {
			// c8:2a:14:03:a7:fb
			return true;
		}

		if(preg_match('/^([0-9a-z]{4}\.){2}[0-9a-z]{4}$/i', $input)) {
			// c82a.1403.a7fb
			return true;
		}

		return false;
	}
}

?>
