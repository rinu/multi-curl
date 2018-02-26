<?php

namespace MultiCurl;

class MultiCurl {
	private $mc;
	private $running;
	private $execStatus;
	private $handlesNotSent = [];

	public function __construct() {
		$this->mc = curl_multi_init();
	}

	public function addCurl($ch) {
		$code = curl_multi_add_handle($this->mc, $ch);

		if ($code === CURLM_OK || $code === CURLM_CALL_MULTI_PERFORM) {
			do {
				$this->execStatus = curl_multi_exec($this->mc, $this->running);
			} while ($this->execStatus === CURLM_CALL_MULTI_PERFORM);

			$key = $this->getKey($ch);
			$this->handlesNotSent[$key] = $ch;
			return $key;
		}
		return null;
	}

	public function sendRequest($key) {
		if (isset($this->handlesNotSent[$key])) {
			while (curl_getinfo($this->handlesNotSent[$key])['pretransfer_time'] === 0.0) {
				usleep(2500);
				$this->execStatus = curl_multi_exec($this->mc, $this->running);
			}
			unset($this->handlesNotSent[$key]);
		}
	}

	public function sendAllRequests() {
		foreach ($this->handlesNotSent as $key => $handle) {
			$this->sendRequest($key);
		}
	}

	public function getNextResult() {
		if ($this->running) {
			while ($this->running && ($this->execStatus == CURLM_OK || $this->execStatus == CURLM_CALL_MULTI_PERFORM)) {
				usleep(2500);
				curl_multi_exec($this->mc, $this->running);

				$responses = $this->readResponses();
				if ($responses !== null) {
					return $responses;
				}
			}
		} else {
			return $this->readResponses();
		}

		return null;
	}

	private function readResponses() {
		$responses = [];
		while ($done = curl_multi_info_read($this->mc)) {
			$key = $this->getKey($done['handle']);

			$done['response'] = curl_multi_getcontent($done['handle']);
			$done['info'] = curl_getinfo($done['handle']);
			$error = curl_error($done['handle']);
			if ($error) {
				$done['error'] = $error;
			}

			$responses[$key] = $done;

			curl_multi_remove_handle($this->mc, $done['handle']);
			curl_close($done['handle']);
		}

		if (!empty($responses)) {
			return $responses;
		}

		return null;
	}

	private function getKey($ch) : string {
		return (string)$ch;
	}
}
