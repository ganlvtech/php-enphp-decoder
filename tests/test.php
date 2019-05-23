<?php

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

define('SAMPLE_DIR', __DIR__ . '/samples');
$filenames = scandir(SAMPLE_DIR);
$filenames = array_values(array_diff($filenames, ['.', '..']));

foreach ($filenames as $key => $filename) {
    echo $filename, PHP_EOL;
    $code = file_get_contents(SAMPLE_DIR . '/' . $filename);
    try {
        $code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
        echo $code, PHP_EOL;
    } catch (Exception $e) {
        echo $e->getTraceAsString();
        exit(1);
    }
}
