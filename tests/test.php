<?php

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);

$code = file_get_contents(__DIR__ . '/assets/admin.php');
$code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);

$code = file_get_contents(__DIR__ . '/assets/index.php');
$code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
