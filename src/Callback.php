<?php

namespace MultiCurl;

class Callback extends MultiCurl {
	private $callbacks = [];

	public function requestSync($ch, callable $callback) {
		$requestKey = $this->requestAsync($ch, $callback);
		$this->read($requestKey);
	}

	public function requestAsync($ch, callable $callback) {
		$requestKey = $this->addCurl($ch);
		$this->sendRequest($requestKey);
		$this->callbacks[$requestKey] = $callback;
		return $requestKey;
	}

	public function read($requestKey = null) {
		$found = false;
		while ($result = $this->getNextResult()) {
			foreach ($result as $responseKey => $response) {
				$this->callbacks[$responseKey]($response);
				if ($requestKey === $responseKey) {
					$found = true;
				}
			}
			if ($found) {
				break;
			}
		}
	}
}
