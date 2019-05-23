<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class BeautifyNodeVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if (($node instanceof Node\Expr\FuncCall
                || $node instanceof Node\Expr\StaticCall
                || $node instanceof Node\Expr\MethodCall)
            && $node->name instanceof Node\Scalar\String_) {
            $node->name = new Node\Name($node->name->value);
        } elseif ($node instanceof Node\Expr\New_
            && $node->class instanceof Node\Scalar\String_) {
            $node->class = new Node\Name($node->class->value);
        } elseif ($node instanceof Node\Scalar\LNumber) {
            $node->setAttribute('kind', Node\Scalar\LNumber::KIND_DEC);
        }
    }
}
