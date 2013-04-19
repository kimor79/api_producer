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
 * APIProducerV2Config
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2Config
 */

class APIProducerV2Config {

	protected $config = array();

	public function __construct() {
		$file = '';
		$var = '';

		if(!defined('API_PRODUCER_MYAPP')) {
			throw new Exception('API_PRODUCER_MYAPP not defined');
		}

		$var = API_PRODUCER_MYAPP . '_CONFIG_FILE';
		$var = strtoupper($var);

		if(!array_key_exists($var, $_SERVER)) {
			throw new Exception(
				sprintf("\$_SERVER['%s'] not defined", $var));
		}

		$file = $_SERVER[$var];

		$this->config = parse_ini_file($file, true);
		if(!is_array($this->config)) {
			throw new Exception('Unable to parse ' . $file);
		}
	}

	public function __deconstruct() {
	}

	/**
	 * Get the value of a config item
	 * @param string $key The key to look up
	 * @param string $default Optional default value
	 * @return mixed The value or NULL
	 */
	public function getValue($key, $default = NULL) {
		if(array_key_exists($key, $this->config)) {
			return $this->config[$key];
		}

		return $default;
	}

	/**
	 * Determines whether a feature is enabled.
	 * e.g., `foo_enable = yes` or `foo_enabled = yes`
	 * These values (case-insensitive) will enable the feature:
	 *  true (bool, string), 1 (int, string), enable[d], on, yes
	 * @param string $feature The feature to look up
	 * @return bool
	 */
	public function isEnabled($feature) {
		$value = $this->getValue($feature . '_enable');

		if(is_null($value)) {
			$value = $this->getValue($feature . '_enabled');
		}

		if(!is_null($value)) {
			if($value === 1) {
				return true;
			}

			if($value === true) {
				return true;
			}

			if(is_string($value)) {
				switch(strtolower($value)) {
					case '1':
					case 'enable':
					case 'enabled':
					case 'on':
					case 'true':
					case 'yes':
						return true;
				}
			}
		}

		return false;
	}
}

?>
