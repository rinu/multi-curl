<?php

require_once(__DIR__ . '/vendor/autoload.php');

function getHandle($sleep) {
	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL => 'http://localhost/sleep.php?sleep=' . $sleep,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_CONNECTTIMEOUT => 5
	]);
	return $ch;
}
