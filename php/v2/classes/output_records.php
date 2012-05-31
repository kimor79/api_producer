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
 * APIProducerV2OutputRecords
 * @author Kimo Rosenbaum <kimor79@yahoo.com>
 * @version $Id$
 * @package APIProducerV2OutputRecords
 */

include_once __DIR__ . '/output.php';

class APIProducerV2OutputRecords extends APIProducerV2Output {

	public function __construct() {
		parent::__construct();

		$this->parameters = array_merge($this->parameters, array(
			'flatOutput' => false,
			'numResults' => 0,
			'sortDir' => 'asc',
			'sortField' => NULL,
			'startIndex' => NULL,
		));
	}

	public function __deconstruct() {
		parent::__deconstruct();
	}

	/**
	 * Send headers, show data
	 * @param array $records
	 * @param int $total
	 * @param int $status
	 * @param string $message
	 * @param array header
	 */
	public function sendData($records, $total, $status = 200, $message = '',
			$misc = array()) {

		$output = array(
			'message' => $message,
			'records' => $records,
			'recordsReturned' => count($records),
			'sortDir' => $this->getParameter('sortDir'),
			'sortField' => $this->getParameter('sortField'),
			'startIndex' => 0,
			'status' => $status,
			'totalRecords' => $total,
		);

		if($this->getParameter('flatOutput')) {
			while(list($key, $value) = each($output['records'])) {
				$output['records'][$key] =
					$this->flattenData($value);
			}
			reset($output['records']);
		}

		// if $misc['headers'] add headers
		// send headers

		$function = 'formatData_' . $this->getParameter('outputFormat');
		if(method_exists($this, $function)) {
			echo $this->$function($output);
		}
	}
}

?>
