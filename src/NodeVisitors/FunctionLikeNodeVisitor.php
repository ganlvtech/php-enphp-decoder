<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FunctionLikeNodeVisitor extends NodeVisitorAbstract
{
    public $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            /** @var $node \PhpParser\Node\Stmt\Function_ */
            return ($this->callback)($node);
        }
        return null;
    }
}
