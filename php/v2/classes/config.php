<?php

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
