<?php
$sleep = $_GET['sleep'] ?? 0;
sleep($sleep);
echo 'Slept ' . $sleep;
