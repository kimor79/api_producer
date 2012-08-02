<?php

/*

Copyright (c) 2012, Kimo Rosenbaum and contributors
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

*/

/**
 * APIProducerV2Input
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Input
 */

include_once __DIR__ . '/validators.php';

class APIProducerV2Input extends APIProducerV2Validators {

	public function __construct() {
	}

	public function __deconstruct() {
	}

	/**
	 * Get the parameters/input from the input (GET, POST, JSON, etc)
	 * @param array $where Where to get input from ('input' => GPJ)
	 * @return mixed array(input, parameters) or false
	 */
	public function getInput($where = array()) {
		$input = array();
		$i_request = array();
		$json = false;
		$params = array();
		$p_request = array();
		$post = false;
		$s_json = '';

		if(!array_key_exists('input', $where)) {
			$where['input'] = 'GPJ';
		}

		if(!array_key_exists('params', $where)) {
			$where['params'] = 'GPJ';
		}

		$s_json = file_get_contents('php://input');
		$json = json_decode($s_json, true);
		if(!$json) {
			$post = true;
		}

		if(stripos($where['input'], 'G') !== false) {
			$i_request = array_merge($i_request, $_GET);
		}

		if(stripos($where['input'], 'J') !== false) {
			if($json) {
				$i_request = array_merge($i_request, $json);
			}
		}

		if(stripos($where['input'], 'P') !== false) {
			if($post) {	
				$i_request = array_merge($i_request, $_POST);
			}
		}

		while(list($key, $value) = each($i_request)) {
			if(!preg_match('/[a-z][A-Z]/', $key)) {
				$input[$key] = $value;
			}
		}
		reset($i_request);

		if(stripos($where['params'], 'G') !== false) {
			$p_request = array_merge($p_request, $_GET);
		}

		if(stripos($where['params'], 'J') !== false) {
			if($json) {
				$p_request = array_merge($p_request, $json);
			}
		}

		if(stripos($where['params'], 'P') !== false) {
			if($post) {
				$p_request = array_merge($p_request, $_POST);
			}
		}

		while(list($key, $value) = each($p_request)) {
			if(preg_match('/[a-z][A-Z]/', $key)) {
				$params[$key] = $value;
			}
		}
		reset($p_request);

		return array($input, $params);
	}

	/**
	 * Do the get_magic_quotes_gpc/stripslashes dance
	 * @param string $input
	 * @return string
	 */
	public function gpcSlash($input) {
		if(get_magic_quotes_gpc()) {
			if(is_string($input)) {
				return stripslashes($input);
			}
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
		reset($input);

		return $output;
	}

	/**
	 * Remove (empty) values
	 * @param array $input
	 * @param array $remove optional list of values (default is '')
	 * @return array
	 */
	public function removeValues($input, $remove = array()) {
		$output = array();

		if(empty($remove)) {
			$remove = array('');
		}

		while(list($key, $value) = each($input)) {
			if(!in_array($value, $remove, true)) {
				$output[$key] = $value;
			}
		}
		reset($input);

		return $output;
	}

	/**
	 * Sanitize input
	 * @param array $input
	 * @param array $sanitize Keys and their sanitize functions
	 * @return array
	 */
	public function sanitizeInput($input, $sanitize) {
		$keys = array_intersect_key($sanitize, $input);
		$output = array_diff_key($input, $sanitize);

		while(list($key, $function) = each($keys)) {
			$multi = false;
			$sanitized = array();

			if(is_array($input[$key])) {
				$values = $input[$key];
			} else {
				$values = array($input[$key]);
			}

			if(substr($function, 0, 7) === '_array_') {
				$function = substr($function, 7);
				$multi = true;
			}

			while(list($junk, $value) = each($values)) {
				$function = 'sanitizeInput_' . $function;
				if(method_exists($this, $function)) {
					$sanitized[] = $this->$function($value);
				} else {
					$sanitized[] = $value;
				}
			}
			reset($values);

			if($multi) {
				$output[$key] = $sanitized;
			} else {
				$output[$key] = $sanitized[0];
			}
		}
		reset($keys);

		return $output;
	}

	/**
	 * Validate input
	 * @param array $input
	 * @param array $required Required keys
	 * @param array $optional Optional keys
	 * @param bool $needed true to check for extraneous keys
 	 * @return array List of errors if any
	 */
	public function validateInput($input, $required, $optional,
			$needed = false) {
		$errors = array();
		$keys = array();

		while(list($key, $function) = each($required)) {
			if(!array_key_exists($key, $input)) {
				$errors[] = 'Missing ' . $key;
				continue;
			}

			$keys[$key] = $function;
		}
		reset($required);

		while(list($key, $function) = each($optional)) {
			if(array_key_exists($key, $input)) {
				$keys[$key] = $function;
			}
		}
		reset($optional);

		if($needed) {
			$extra = array_diff_key($input, $required, $optional);

			while(list($key, $junk) = each($extra)) {
				$errors[] = 'Extraneous ' . $key;
			}
			reset($extra);
		}

		while(list($key, $function) = each($keys)) {
			$multi = false;
			$values = (array) $input[$key];

			if(substr($function, 0, 7) === '_array_') {
				$function = substr($function, 7);
				$multi = true;
			}

			if(!is_scalar($input[$key])) {
				if(!$multi) {
					$errors[] = 'Multiple "' . $key .
						'" not allowed';
					continue;
				}
			}

			if(empty($function)) {
				continue;
			}

			$function = 'validateInput_' . $function;
			if(!method_exists($this, $function)) {
				$errors[] = 'Unable to validate ' . $key;
				continue;
			}

			while(list($junk, $value) = each($values)) {
				if(!$this->$function($value)) {
					$errors[] = 'Invalid ' . $key;
				}
			}
		}
		reset($keys);

		return $errors;
	}
}

?>
