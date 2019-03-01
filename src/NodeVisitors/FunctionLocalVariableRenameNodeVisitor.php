<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FunctionLocalVariableRenameNodeVisitor extends NodeVisitorAbstract
{
    public $varMap = [];
    public $argCount = 0;
    public $localVarCount = 0;

    public static function isUnreadable($string)
    {
        return 1 === preg_match('/\W/', $string);
    }

    public function generateArgName()
    {
        return 'arg' . ($this->argCount++);
    }

    public function generateLocalVarName()
    {
        return 'v' . ($this->localVarCount++);
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\FunctionLike) {
            /** @var $node \PhpParser\Node\Stmt\Function_ */
            foreach ($node->params as $param) {
                $name = $param->var->name;
                if (array_key_exists($name, $this->varMap)) {
                    $param->var->name = $this->varMap[$name];
                } elseif (self::isUnreadable($name)) {
                    $this->varMap[$name] = $this->generateArgName();
                    $param->var->name = $this->varMap[$name];
                }
            }
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
            $name = $node->name;
            if (array_key_exists($name, $this->varMap)) {
                $node->name = $this->varMap[$name];
            } elseif (self::isUnreadable($name)) {
                $this->varMap[$name] = $this->generateLocalVarName();
                $node->name = $this->varMap[$name];
            }
        }
        return null;
    }
}
