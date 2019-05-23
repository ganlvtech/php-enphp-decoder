<?php
/**
 * EnPHP Decoder
 *
 * https://github.com/ganlvtech/php-enphp-decoder
 *
 * Copyright (C) 2019  Ganlv
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Ganlv\EnphpDecoder;

use Ganlv\EnphpDecoder\NodeVisitors\BeautifyNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\FindAndRemoveGlobalVariableNameNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\FunctionGlobalStringNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\FunctionLikeNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\FunctionLocalVariableRenameNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\GlobalStringNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\RemoveDefineGlobalVariableNameNodeVisitor;
use Ganlv\EnphpDecoder\NodeVisitors\RemoveUnusedConstFetchNodeVisitor;
use Ganlv\EnphpDecoder\PrettyPrinter\StandardPrettyPrinter;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class AutoDecoder
{
    protected $ast;
    protected $globalVarName;
    protected $globalVarKey;
    protected $globalVarKeyExpr;
    protected $delimiter;
    protected $data;
    protected $start;
    protected $length;
    protected $stringArray;
    protected $dataType;
    protected $globalVarKeyType;

    public function __construct($ast)
    {
        $this->ast = $ast;
    }

    public function findAndRemoveGlobalVariableName()
    {
        $nodeVisitor = new FindAndRemoveGlobalVariableNameNodeVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        $this->globalVarName = $nodeVisitor->globalVarName;
        $this->globalVarKey = $nodeVisitor->globalVarKey;
        $this->globalVarKeyExpr = $nodeVisitor->globalVarKeyExpr;
        $this->delimiter = $nodeVisitor->delimiter;
        $this->data = $nodeVisitor->data;
        $this->start = $nodeVisitor->start;
        $this->length = $nodeVisitor->length;
        $this->dataType = $nodeVisitor->dataType;
        $this->globalVarKeyType = $nodeVisitor->globalVarKeyType;
        return $this->ast;
    }

    public function decodeStringArray()
    {
        switch ($this->dataType) {
            case 0:
                break;
            case FindAndRemoveGlobalVariableNameNodeVisitor::DATA_TYPE_EXPLODE_GZINFLATE_SUBSTR:
                $this->stringArray = explode($this->delimiter, gzinflate(substr($this->data, $this->start, $this->length)));
                break;
            case FindAndRemoveGlobalVariableNameNodeVisitor::DATA_TYPE_EXPLODE:
                $this->stringArray = explode($this->delimiter, $this->data);
                break;
        }
    }

    public function removeDefineGlobalVariableName()
    {
        $nodeVisitor = new RemoveDefineGlobalVariableNameNodeVisitor($this->globalVarKey);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function removeUnusedConstFetchNodeVisitor()
    {
        $nodeVisitor = new RemoveUnusedConstFetchNodeVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function replaceGlobalString()
    {
        $nodeVisitor = new GlobalStringNodeVisitor($this->globalVarName, $this->globalVarKeyExpr, $this->stringArray);
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function replaceFunctionLikeGlobalString()
    {
        $globalVarName = $this->globalVarName;
        $globalVarKeyExpr = $this->globalVarKeyExpr;
        $stringArray = $this->stringArray;
        $nodeVisitor = new FunctionLikeNodeVisitor(function ($node) use ($globalVarName, $globalVarKeyExpr, $stringArray) {
            /** @var $node \PhpParser\Node\Stmt\Function_ */
            $nodeVisitor = new FunctionGlobalStringNodeVisitor($globalVarName, $globalVarKeyExpr, $stringArray);
            $traverser = new NodeTraverser();
            $traverser->addVisitor($nodeVisitor);
            $node->stmts = $traverser->traverse($node->stmts);
            return null;
        });
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function renameFunctionLikeLocalVariable()
    {
        $nodeVisitor = new FunctionLikeNodeVisitor(function ($node) {
            /** @var $node \PhpParser\Node\Stmt\Function_ */
            $nodeVisitor = new FunctionLocalVariableRenameNodeVisitor();
            $traverser = new NodeTraverser();
            $traverser->addVisitor($nodeVisitor);
            $ast = $traverser->traverse([$node]);
            return $ast[0];
        });
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function beautify()
    {
        $nodeVisitor = new BeautifyNodeVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($nodeVisitor);
        $this->ast = $traverser->traverse($this->ast);
        return $this->ast;
    }

    public function prettyPrintFile()
    {
        return StandardPrettyPrinter::prettyPrinter()->prettyPrintFile($this->ast);
    }

    /**
     * @return bool is ast modified
     */
    public function autoDecode()
    {
        $modified = false;
        for ($i = 0; $i < 10; $i++) { // avoid too many loops
            $this->findAndRemoveGlobalVariableName();
            if ($this->dataType === 0) {
                break;
            }
            $modified = true;
            $this->decodeStringArray();
            $this->removeDefineGlobalVariableName();
            $this->removeUnusedConstFetchNodeVisitor();
            $this->replaceGlobalString();
            $this->replaceFunctionLikeGlobalString();
            $this->renameFunctionLikeLocalVariable();
            $this->beautify();
        }
        return $modified;
    }

    public static function parseFile($code)
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP5);
        $ast = $parser->parse($code);
        return $ast;
    }

    public static function decode($code)
    {
        $ast = self::parseFile($code);
        $decoder = new self($ast);
        $modified = $decoder->autoDecode();
        if ($modified) {
            return $decoder->prettyPrintFile();
        } else {
            return $code;
        }
    }
}
