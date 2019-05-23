<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class RemoveDefineGlobalVariableNameNodeVisitor extends NodeVisitorAbstract
{
    public $globalVarKey;

    public function __construct($globalVarKey)
    {
        $this->globalVarKey = $globalVarKey;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof \PhpParser\Node\Stmt\Expression
            && $node->expr instanceof \PhpParser\Node\Expr\FuncCall
            && $node->expr->name instanceof \PhpParser\Node\Name
            && count($node->expr->name->parts) === 1
            && $node->expr->name->parts[0] === 'define'
            && count($node->expr->args) === 2
            && $node->expr->args[0] instanceof \PhpParser\Node\Arg
            && $node->expr->args[0]->value instanceof \PhpParser\Node\Scalar\String_
            && $node->expr->args[0]->value->value === $this->globalVarKey
            && $node->expr->args[0]->byRef === false
            && $node->expr->args[0]->unpack === false
            && $node->expr->args[1] instanceof \PhpParser\Node\Arg
            && $node->expr->args[1]->value instanceof \PhpParser\Node\Scalar\String_
            && $node->expr->args[1]->byRef === false
            && $node->expr->args[1]->unpack === false) {
            return NodeTraverser::REMOVE_NODE;
        }
    }
}
