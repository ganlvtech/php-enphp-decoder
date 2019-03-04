<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FunctionGlobalStringNodeVisitor extends NodeVisitorAbstract
{
    public $localVarName;
    public $globalVarName;
    public $globalVarKey;
    public $stringArray;

    public function __construct($globalVarName, $globalVarKey, $stringArray)
    {
        $this->globalVarName = $globalVarName;
        $this->globalVarKey = $globalVarKey;
        $this->stringArray = $stringArray;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Expression
            && $node->expr instanceof Node\Expr\AssignRef
            && $node->expr->var instanceof Node\Expr\Variable
            && $node->expr->expr instanceof Node\Expr\ArrayDimFetch
            && $node->expr->expr->var instanceof Node\Expr\Variable
            && $node->expr->expr->var->name === $this->globalVarName
            && $node->expr->expr->dim instanceof Node\Expr\ConstFetch
            && $node->expr->expr->dim->name instanceof Node\Name
            && $node->expr->expr->dim->name->parts[0] === $this->globalVarKey
        ) {
            $this->localVarName = $node->expr->var->name;
            return NodeTraverser::REMOVE_NODE;
        } elseif ($node instanceof Node\Expr\ArrayDimFetch
            && $node->var instanceof Node\Expr\Variable
            && $node->var->name === $this->localVarName
            && $node->dim instanceof Node\Scalar\LNumber
        ) {
            return new Node\Scalar\String_($this->stringArray[$node->dim->value]);
        }
        return null;
    }
}
