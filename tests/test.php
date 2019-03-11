<?php

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$code = file_get_contents(__DIR__ . '/assets/admin.php');
try {
    $code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
    echo $code, PHP_EOL;
} catch (Exception $e) {
    echo $e->getTraceAsString();
    exit(1);
}

$code = file_get_contents(__DIR__ . '/assets/index.php');
try {
    $code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
    echo $code, PHP_EOL;
} catch (Exception $e) {
    echo $e->getTraceAsString();
    exit(2);
}
