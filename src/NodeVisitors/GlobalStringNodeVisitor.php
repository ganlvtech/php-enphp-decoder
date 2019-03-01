<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class GlobalStringNodeVisitor extends NodeVisitorAbstract
{
    public $globalVarName;
    public $stringArray;

    public function __construct($globalVarName, $stringArray)
    {
        $this->globalVarName = $globalVarName;
        $this->stringArray = $stringArray;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\ArrayDimFetch
            && $node->var instanceof Node\Expr\ArrayDimFetch
            && $node->var->var instanceof Node\Expr\Variable
            && $node->var->var->name === 'GLOBALS'
            && $node->var->dim instanceof Node\Expr\ConstFetch
            && $node->var->dim->name instanceof Node\Name
            && $node->var->dim->name->parts[0] === $this->globalVarName
            && $node->dim instanceof Node\Scalar\LNumber
        ) {
            return new Node\Scalar\String_($this->stringArray[$node->dim->value]);
        }
        return null;
    }
}
