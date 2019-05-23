<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use Ganlv\EnphpDecoder\KnownEnphpBugs\ExplodeDelimiterWithApostropheException;
use Ganlv\EnphpDecoder\PrettyPrinter\StandardPrettyPrinter;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class GlobalStringNodeVisitor extends NodeVisitorAbstract
{
    public $globalVarName;
    public $globalVarKeyExpr;
    public $stringArray;

    public function __construct($globalVarName, $globalVarKeyExpr, $stringArray)
    {
        $this->globalVarName = $globalVarName;
        $this->globalVarKeyExpr = $globalVarKeyExpr;
        $this->stringArray = $stringArray;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\ArrayDimFetch
            && $node->var instanceof Node\Expr\ArrayDimFetch
            && $node->var->var instanceof Node\Expr\Variable
            && $node->var->var->name === $this->globalVarName
            && $node->var->dim !== null
            && StandardPrettyPrinter::prettyPrinter()->prettyPrintExpr($node->var->dim) === $this->globalVarKeyExpr
            && $node->dim instanceof Node\Scalar\LNumber) {
            ExplodeDelimiterWithApostropheException::test($this->stringArray, $node->dim->value);
            return new Node\Scalar\String_($this->stringArray[$node->dim->value]);
        }
        return null;
    }
}
