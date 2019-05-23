<?php

namespace Ganlv\EnphpDecoder\PrettyPrinter;

use PhpParser\PrettyPrinter\Standard;

class StandardPrettyPrinter
{
    public static function prettyPrinter(): Standard
    {
        static $prettyPrinter = null;
        if (is_null($prettyPrinter)) {
            $prettyPrinter = new Standard();
        }
        return $prettyPrinter;
    }
}
