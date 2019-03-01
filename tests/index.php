<?php

require __DIR__ . '/../vendor/autoload.php';

$code = file_get_contents(__DIR__ . '/assets/admin.php');
echo \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
