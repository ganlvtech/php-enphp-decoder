<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class RemoveUnusedConstFetchNodeVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Expression
            && $node->expr instanceof Node\Expr\ConstFetch
            && $node->expr->name instanceof Node\Name
            && count($node->expr->name->parts) === 1) {
            return NodeTraverser::REMOVE_NODE;
        }
        return null;
    }
}
