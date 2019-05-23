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

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function decodeRecursive($dir)
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (endsWith($path, '.php')) {
                echo $path;
                $modified = false;
                try {
                    $code = file_get_contents($path);
                    $ast = AutoDecoder::parseFile($code);
                    $decoder = new AutoDecoder($ast);
                    $modified = $decoder->autoDecode();
                    if ($modified) {
                        file_put_contents($path, $decoder->prettyPrintFile());
                        echo '  Decoded.';
                    }
                } catch (KnownEnphpBugsException $e) {
                    echo '  Known EnPHP Bugs: ', $e->getMessage();
                } catch (Throwable $e) {
                    echo '  Decode error.', PHP_EOL;
                    echo $e->getTraceAsString();
                }
                if (!$modified) {
                    echo '  Unchanged.';
                }
                echo PHP_EOL;
            }
        } else {
            if ($value != '.' && $value != '..') {
                decodeRecursive($path);
            }
        }
    }
}

decodeRecursive($argv[1]);
