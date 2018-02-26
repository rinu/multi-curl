<?php

require(__DIR__ . '/bootstrap.php');

$totalTime = microtime(true);

$multi = new \MultiCurl\Callback();

$maxSleep = 3;

$multi->requestAsync(getHandle($maxSleep), function($response) {
	echo "Async request completed in {$response['info']['total_time']}\n";
	echo $response['response'] . "\n";
});

$multi->requestSync(getHandle($maxSleep - 1), function($response) {
	echo "Sync request completed in {$response['info']['total_time']}\n";
	echo $response['response'] . "\n";
});

$multi->read();

$totalTime = microtime(true) - $totalTime;
echo 'Total time: ' . $totalTime . "\n";

assert($maxSleep === (int)floor($totalTime), 'Script execution should not take much longer than the longest curl request');
