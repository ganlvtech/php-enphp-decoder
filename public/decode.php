<?php
/**
 * EnPHP Decoder
 *
 * https://github.com/ganlvtech/php-enphp-decoder
 *
 * Copyright (C) 2019  Ganlv
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

error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo 'File upload error. Refresh the page and try again please.';
    return;
}

$code = file_get_contents($_FILES['file']['tmp_name']);
try {
    $decoded = AutoDecoder::decode($code);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . urlencode($_FILES['file']['name']) . '"');
    echo $decoded;
} catch (KnownEnphpBugsException $exception) {
    echo 'Known EnPHP bugs: ', $exception->getMessage(), PHP_EOL;
    echo 'See: <a href="https://github.com/ganlvtech/php-enphp-decoder/blob/master/docs/enphp_bugs.md">docs/enphp_bugs.md</a>';
} catch (\PhpParser\Error $exception) {
    echo 'Parser error: ', $exception->getMessage(), PHP_EOL;
    echo 'Your php file is not a valid php file. Are you sure it can be run on your machine?';
} catch (Exception $exception) {
    echo 'Unknown error: ', $exception->getMessage(), PHP_EOL;
    echo 'You can <a href="https://github.com/ganlvtech/php-enphp-decoder/issues/new">submit an issue</a>, if you are are that this file is encoded by EnPHP.';
}
