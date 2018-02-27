<?php

require(__DIR__ . '/bootstrap.php');

$totalTime = microtime(true);

$multi = new \MultiCurl\Callback();

$maxSleep = 3;

$asyncCallback = function ($response) {
	echo "Async request completed in {$response['info']['total_time']}\n";
	echo $response['response'] . "\n";
};

$multi->requestAsync(getHandle($maxSleep - 2), $asyncCallback);
$multi->requestAsync(getHandle($maxSleep), $asyncCallback);
$multi->requestAsync(getHandle($maxSleep - 1), $asyncCallback);

$multi->requestSync(getHandle($maxSleep - 1), function($response) {
	echo "Sync request completed in {$response['info']['total_time']}\n";
	echo $response['response'] . "\n";
});

$multi->read();

$totalTime = microtime(true) - $totalTime;
echo 'Total time: ' . $totalTime . "\n";

assert($maxSleep === (int)floor($totalTime), 'Script execution should not take much longer than the longest curl request');
