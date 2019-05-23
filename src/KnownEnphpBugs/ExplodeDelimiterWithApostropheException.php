<?php

namespace Ganlv\EnphpDecoder\KnownEnphpBugs;

class ExplodeDelimiterWithApostropheException extends KnownEnphpBugsException
{
    public $stringArray;
    public $dim;

    public function __construct($stringArray, $dim)
    {
        $this->stringArray = $stringArray;
        $this->dim = $dim;
        parent::__construct('explode delimiter with apostrophe.');
    }

    public static function test($stringArray, $dim)
    {
        if (!isset($stringArray[$dim]) && count($stringArray) === 1 && strpos($stringArray[0],'\\\'') !== false) {
            throw new self($stringArray, $dim);
        }
    }
}
