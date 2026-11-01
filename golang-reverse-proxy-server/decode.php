<?php
/**
 * EnPHP Decoder
 *
 * https://github.com/ganlvtech/php-enphp-decoder
 *
 * Copyright (C) 2026  Ganlv
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use Ganlv\EnphpDecoder\AutoDecoder;
use Ganlv\EnphpDecoder\KnownEnphpBugs\KnownEnphpBugsException;

date_default_timezone_set('Asia/Shanghai');

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$code = file_get_contents('php://stdin');

// Log
$start_time = microtime(true);
file_put_contents('public/log.txt', date('Y-m-d H:i:s', $start_time) . "\t" . $_ENV['REMOTE_ADDR'] . "\t" . strlen($code) . "B\t" . $_ENV['FILE_NAME'] . "\n", FILE_APPEND);

try {
    $decoded = AutoDecoder::decode($code);
    echo "OK", PHP_EOL;
    echo $decoded;
    $result = 'OK';
} catch (KnownEnphpBugsException $exception) {
    echo 'Known EnPHP bugs: ', $exception->getMessage(), PHP_EOL;
    echo 'See: <a href="https://github.com/ganlvtech/php-enphp-decoder/blob/master/docs/enphp_bugs.md">docs/enphp_bugs.md</a>';
    $result = 'Known EnPHP bugs: ' . $exception->getMessage();
} catch (\PhpParser\Error $exception) {
    echo 'Parser error: ', $exception->getMessage(), PHP_EOL;
    echo 'Your php file is not a valid php file. Are you sure it can be run on your machine?';
    $result = 'Parser error: ' . $exception->getMessage();
} catch (Exception $exception) {
    echo 'Unknown error: ', $exception->getMessage(), PHP_EOL;
    echo 'You can <a href="https://github.com/ganlvtech/php-enphp-decoder/issues/new">submit an issue</a>, if you are are that this file is encoded by EnPHP.';
    $result = 'Unknown error: ' . $exception->getMessage();
}

// Log
$end_time = microtime(true);
file_put_contents('public/result.txt', date('Y-m-d H:i:s', $end_time) . "\t" . $_ENV['REMOTE_ADDR'] . "\t" . (($end_time - $start_time) * 1000) . "ms\t" . strlen($code) . " B -> " . strlen($decoded) . " B\t" . $_ENV['FILE_NAME'] . "\t" . $result . "\n", FILE_APPEND);
