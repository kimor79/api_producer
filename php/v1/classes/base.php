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

	public $multi_separator = ',';

	protected $output_formats = array(
		'json',
		'print_r',
	);

	protected $parameters = array();

	protected $requires_flat_output = array();

	public function __construct() {
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
	 * Sanitize input
	 * @param array $input
	 * @param array $sanitize
	 * @return array
	 */
	public function sanitizeInput($input = array(), $sanitize = array()) {
		$output = $input;

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
					$tests = explode($this->multi_separator, $input[$key]);
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
	 * gpcSlash it
	 * @param string $input
	 * @return string
	 */
	protected function sanitizeInput_gpcSlash($input) {
		return $this->gpcSlash($input);
	}

	/**
	 * Sanitize contentType value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_contentType($value) {
		return $this->trueFalse($value, $this->api_parameters['contentType']);
	}

	/**
	 * Sanitize flatOutput value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_flatOutput($value) {
		return $this->trueFalse($value, $this->api_parameters['flatOutput']);
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
		return $this->trueFalse($value, $this->api_parameters['subDetails']);
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
				header('Content-Type: ' . $this->content_types[$format]);
			}
		}

		if(array_key_exists($format, $this->content_disposition)) {
			if(!is_null($this->content_disposition[$format])) {
				if(is_null($filename)) {
					$filename = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1) . time();
				}

				header(sprintf("Content-Disposition: attachment; filename=\"%s.%s\"",
					$filename, $this->content_disposition[$format]));
			}
		}
	}

	/**
	 * Remove API parameters from input
	 * @param array $input
	 * @return array
	 */
	public function setInput($input = array()) {
		$output = array();

		foreach($input as $key => $value) {
			if(!array_key_exists($key, $this->parameters)) {
				$output[$key] = $value;
			}
		}

		return $output;
	}

	/**
	 * Set the API parameters from request (eg $_GET, $_POST)
	 * @param array $input array containing input to look for parameters [default = $_GET]
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
				$this->parameters[$param] = $this->$function($value);
			} else {
				$this->parameters[$param] = $value;
			}
		}

		if(in_array($this->parameters['outputFormat'], $this->requires_flat_output)) {
			if(!is_null($this->requires_flat_output[$this->parameters['outputFormat']])) {
				$this->parameters['flatOutput'] = true;
			}
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
	public function validateInput($input = array(), $required = array(), $optional = array(), $needed = false) {
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

			foreach($extra as $key) {
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
					$errors[] = 'Multiple ' . $key . ' not allowed';
					continue;
				}

				$tests = $input[$key];
			} else {
				if($multi) {
					$tests = explode($this->multi_separator, $input[$key]);
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
}

?>
