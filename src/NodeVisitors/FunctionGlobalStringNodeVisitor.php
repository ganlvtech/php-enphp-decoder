<?php

namespace Ganlv\EnphpDecoder\NodeVisitors;

use Ganlv\EnphpDecoder\KnownEnphpBugs\ExplodeDelimiterWithApostropheException;
use Ganlv\EnphpDecoder\PrettyPrinter\StandardPrettyPrinter;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class FunctionGlobalStringNodeVisitor extends NodeVisitorAbstract
{
    public $localVarName;
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
        if ($node instanceof Node\Stmt\Expression
            && $node->expr instanceof Node\Expr\AssignRef
            && $node->expr->var instanceof Node\Expr\Variable
            && $node->expr->expr instanceof Node\Expr\ArrayDimFetch
            && $node->expr->expr->var instanceof Node\Expr\Variable
            && $node->expr->expr->var->name === $this->globalVarName
            && $node->expr->expr->dim !== null
            && StandardPrettyPrinter::prettyPrinter()->prettyPrintExpr($node->expr->expr->dim) === $this->globalVarKeyExpr) {
            $this->localVarName = $node->expr->var->name;
            return NodeTraverser::REMOVE_NODE;
        } elseif ($node instanceof Node\Expr\ArrayDimFetch
            && $node->var instanceof Node\Expr\Variable
            && $node->var->name === $this->localVarName
            && $node->dim instanceof Node\Scalar\LNumber) {
            ExplodeDelimiterWithApostropheException::test($this->stringArray, $node->dim->value);
            return new Node\Scalar\String_($this->stringArray[$node->dim->value]);
        }
        return null;
    }
}
