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
 * @package ApiProducerRecords
 */

include('base.php');

class ApiProducerRecords extends ApiProducerBase {

	public function __construct() {
		parent::__construct();

		$this->api_parameters = array_merge(
			$this->api_parameters, array(
			'csvHeader' => true,
			'numResults' => 0,
			'outputFields' => array(),
			'sortDir' => 'desc',
			'sortField' => NULL,
			'startIndex' => 0,
		));

		$this->content_disposition['csv'] = 'csv';
		$this->content_types['csv'] = 'text/csv';

		$this->output_formats[] = 'csv';
		$this->output_formats[] = 'list';

		$this->requires_flat_output['csv'] = true;
	}

	/**
	 * Sanitize csvHeader value
	 * @param string $value
	 * @return bool
	 */
	protected function sanitizeParameter_csvHeader($value) {
		return $this->trueFalse($value, $this->api_parameters['csvHeader']);
	}

	/**
	 * Sanitize numResults value
	 * @param string $value
	 * @return int
	 */
	protected function sanitizeParameter_numResults($value) {
		if(ctype_digit((string) $value)) {
			return (int) $value;
		}

		return $this->api_parameters['numResults'];
	}

	/**
	 * Sanitize outputFields value
	 * @param mixed $value
	 * @return array
	 */
	protected function sanitizeParameter_outputFields($value) {
		$fields = array();
		$keys = array();

		if(is_array($value)) {
			$keys = $value;
		} else {
			$keys = explode(',', $value);
		}

		foreach($keys as $key) {
			if($key == '') {
				continue;
			}

			if(substr($key, 0, 1) == '!') {
				$fields[$key] = false;
			} else {
				$fields[$key] = true;
			}
		}

		return $fields;
	}

	/**
	 * Sanitize sortDir value
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_sortDir($value) {
		switch(strtolower((string) $value)) {
			case 'asc':
			case 'ascending':
				return 'asc';
				break;
			case 'desc':
			case 'descending':
				return 'desc';
				break;
		}

		return $this->api_parameters['sortDir'];
	}

	/**
	 * Sanitize sortField value
	 * @param string $value
	 * @return string
	 */
	protected function sanitizeParameter_sortField($value) {
		if(is_null($value) || is_scalar($value)) {
			return $value;
		}

		return $this->api_parameters['sortField'];
	}

	/**
	 * Sanitize startIndex value
	 * @param string $value
	 * @return int
	 */
	protected function sanitizeParameter_startIndex($value) {
		if(ctype_digit((string) $value)) {
			return (int) $value;
		}

		return $this->api_parameters['startIndex'];
	}

	/**
	 * Show output
	 * @param array $records
	 * @param int $total
	 * @param int $status Optional
	 * @param string $message Optional
	 */
	public function showOutput($records = array(), $total = 0, $status = 200, $message = '') {
		$count = count($records);

		$output = array(
			'message' => $message,
			'records' => $records,
			'recordsReturned' => $count,
			'sortDir' => $this->getParameter('sortDir'),
			'startIndex' => $this->getParameter('startIndex'),
			'status' => $status,
			'totalRecords' => ($total) ? $total : $count,
		);

		$function = 'showOutput_' . $this->getParameter('outputFormat');
		if(method_exists($this, $function)) {
			$this->$function($output);
			return;
		}

		// Fallback
		print_r($output);
	}

	/**
	 * Show csv output
	 * @param array $data
	 */
	protected function showOutput_csv($data) {
		$values = $data['records'];

		$fp = fopen('php://temp', 'r+');

		if($this->getParameter('csvHeader')) {
			$first = reset($data['records']);
			if(is_array($first)) {
				fputcsv($fp, array_keys($first), ',', '"');
			}
		}

		foreach($values as $value) {
			fputcsv($fp, (array) $value, ',', '"');
		}

		rewind($fp);

		$stat = fstat($fp);
		header('Content-Length: ' . $stat['size']);

		fpassthru($fp);
		fclose($fp);
	}

	/**
	 * Show list output
	 * @param array $data
	 */
	protected function showOutput_list($data) {
		$output = array();

		foreach($data['records'] as $record) {
			if(is_array($record)) {
				$output[] = $record[$this->outputFormat_list_key];
			} else {
				$output[] = $record;
			}
		}

		echo implode("\n", $output);
	}
}

?>
