<?php

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

define('SAMPLE_DIR', __DIR__ . '/samples');
$filenames = scandir(SAMPLE_DIR);
$filenames = array_values(array_diff($filenames, ['.', '..']));

foreach ($filenames as $key => $filename) {
    $path = SAMPLE_DIR . '/' . $filename;
    echo $path, PHP_EOL;
    $code = file_get_contents($path);
    try {
        $code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
        echo $code, PHP_EOL;
    } catch (Exception $e) {
        echo $e->getTraceAsString();
        exit(1);
    }
}

define('BUG_SAMPLE_DIR', __DIR__ . '/bug_samples');
$filenames = scandir(BUG_SAMPLE_DIR);
$filenames = array_values(array_diff($filenames, ['.', '..']));

foreach ($filenames as $key => $filename) {
    $path = BUG_SAMPLE_DIR . '/' . $filename;
    echo $path, PHP_EOL;
    $code = file_get_contents($path);
    try {
        $code = \Ganlv\EnphpDecoder\AutoDecoder::decode($code);
        echo $code, PHP_EOL;
        exit(3);
    } catch (\Ganlv\EnphpDecoder\KnownEnphpBugs\KnownEnphpBugsException $e) {
        echo 'KnownEnphpBugsException: ', $e->getMessage(), PHP_EOL;
    } catch (Exception $e) {
        echo $e->getTraceAsString();
        exit(2);
    }
}
