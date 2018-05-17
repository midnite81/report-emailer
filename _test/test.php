<?php
include(__DIR__ . '/../src/ReportEmailer.php');
include(__DIR__ . '/Dave.php');
include(__DIR__ . '/../vendor/autoload.php');

$d = new Dave();
$d->run();

