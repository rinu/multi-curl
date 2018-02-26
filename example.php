<?php

require(__DIR__ . '/bootstrap.php');

$totalTime = microtime(true);

$multi = new \MultiCurl\MultiCurl();

$maxSleep = 5;

$keys = [];
$addCurlHandles = microtime(true);
$keys[] = $multi->addCurl(getHandle($maxSleep));
for ($i = 0; $i < 2; $i++) {
	$keys[] = $multi->addCurl(getHandle(random_int(1, $maxSleep)));
}
echo 'Add curl handles: ' . (microtime(true) - $addCurlHandles) . "\n";

$sendRequests = microtime(true);
$multi->sendAllRequests();
echo 'Sending requests: ' . (microtime(true) - $sendRequests) . "\n";

/**/
$loop = microtime(true);
while (microtime(true) - $loop < 2) {
	usleep(100);
}
echo 'Loop: ' . (microtime(true) - $loop) . "\n";
/**/

$getResults = microtime(true);
while ($result = $multi->getNextResult()) {
	foreach ($result as $key => $response) {
		echo $response['response'] . "\n";
	}
}
echo 'Get results: ' . (microtime(true) - $getResults) . "\n";

$totalTime = microtime(true) - $totalTime;
echo 'Total time: ' . $totalTime . "\n";

assert($maxSleep === (int)floor($totalTime), 'Script execution should not take much longer than the longest curl request');
