<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FindAndRemoveGlobalVariableNameNodeVisitor extends NodeVisitorAbstract
{
    public $globalVarName;
    public $globalVarKey;
    public $delimiter;
    public $data;
    public $start;
    public $length;

    public function leaveNode(Node $node)
    {
        if ($node instanceof \PhpParser\Node\Stmt\Expression
            && $node->expr instanceof \PhpParser\Node\Expr\Assign
            && $node->expr->var instanceof \PhpParser\Node\Expr\ArrayDimFetch
            && $node->expr->var->var instanceof \PhpParser\Node\Expr\Variable
            && $node->expr->var->dim instanceof \PhpParser\Node\Expr\ConstFetch
            && $node->expr->var->dim->name instanceof \PhpParser\Node\Name
            && count($node->expr->var->dim->name->parts) === 1
            && $node->expr->expr instanceof \PhpParser\Node\Expr\FuncCall
            && $node->expr->expr->name instanceof \PhpParser\Node\Name
            && count($node->expr->expr->name->parts) === 1
            && $node->expr->expr->name->parts[0] === 'explode'
            && count($node->expr->expr->args) === 2
            && $node->expr->expr->args[0] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[0]->value instanceof \PhpParser\Node\Scalar\String_
            && $node->expr->expr->args[0]->byRef === false
            && $node->expr->expr->args[0]->unpack === false
            && $node->expr->expr->args[1] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[1]->value instanceof \PhpParser\Node\Expr\FuncCall
            && $node->expr->expr->args[1]->value->name instanceof \PhpParser\Node\Name
            && count($node->expr->expr->args[1]->value->name->parts) === 1
            && $node->expr->expr->args[1]->value->name->parts[0] === 'gzinflate'
            && count($node->expr->expr->args[1]->value->args) === 1
            && $node->expr->expr->args[1]->value->args[0] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[1]->value->args[0]->value instanceof \PhpParser\Node\Expr\FuncCall
            && $node->expr->expr->args[1]->value->args[0]->value->name instanceof \PhpParser\Node\Name
            && count($node->expr->expr->args[1]->value->args[0]->value->name->parts) === 1
            && $node->expr->expr->args[1]->value->args[0]->value->name->parts[0] === 'substr'
            && count($node->expr->expr->args[1]->value->args[0]->value->args) === 3
            && $node->expr->expr->args[1]->value->args[0]->value->args[0] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[1]->value->args[0]->value->args[0]->value instanceof \PhpParser\Node\Scalar\String_
            && $node->expr->expr->args[1]->value->args[0]->value->args[0]->byRef === false
            && $node->expr->expr->args[1]->value->args[0]->value->args[0]->unpack === false
            && $node->expr->expr->args[1]->value->args[0]->value->args[1] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[1]->value->args[0]->value->args[1]->value instanceof \PhpParser\Node\Scalar\LNumber
            && $node->expr->expr->args[1]->value->args[0]->value->args[1]->byRef === false
            && $node->expr->expr->args[1]->value->args[0]->value->args[1]->unpack === false
            && $node->expr->expr->args[1]->value->args[0]->value->args[2] instanceof \PhpParser\Node\Arg
            && $node->expr->expr->args[1]->value->args[0]->value->args[2]->value instanceof \PhpParser\Node\Expr\UnaryMinus
            && $node->expr->expr->args[1]->value->args[0]->value->args[2]->value->expr instanceof \PhpParser\Node\Scalar\LNumber
            && $node->expr->expr->args[1]->value->args[0]->value->args[2]->byRef === false
            && $node->expr->expr->args[1]->value->args[0]->value->args[2]->unpack === false
            && $node->expr->expr->args[1]->value->args[0]->byRef === false
            && $node->expr->expr->args[1]->value->args[0]->unpack === false
            && $node->expr->expr->args[1]->byRef === false
            && $node->expr->expr->args[1]->unpack === false
        ) {
            $this->globalVarName = $node->expr->var->var->name;
            $this->globalVarKey = $node->expr->var->dim->name->parts[0];
            $this->delimiter = $node->expr->expr->args[0]->value->value;
            $this->data = $node->expr->expr->args[1]->value->args[0]->value->args[0]->value->value;
            $this->start = $node->expr->expr->args[1]->value->args[0]->value->args[1]->value->value;
            $this->length = -$node->expr->expr->args[1]->value->args[0]->value->args[2]->value->expr->value;
            return NodeTraverser::REMOVE_NODE;
        }
    }
}
