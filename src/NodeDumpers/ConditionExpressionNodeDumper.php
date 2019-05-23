<?php

namespace Ganlv\EnphpDecoder\NodeDumpers;

use PhpParser\Node;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;

/**
 * Class ConditionExpressionNodeDumper
 *
 * Usage:
 *
 *     echo (new ConditionExpressionNodeDumper)->dump($ast);
 *     echo (new ConditionExpressionNodeDumper)->dump($node, '$node');
 *
 * @package Ganlv\EnphpDecoder\NodeDumpers
 */
class ConditionExpressionNodeDumper
{
    private $dumpPrefix = '';

    public function dump($node, $dumpPrefix = '$ast')
    {
        $this->dumpPrefix = $dumpPrefix;
        return $this->dumpRecursive($node);
    }

    protected function dumpRecursive($node)
    {
        $result = [];
        if ($node instanceof Node) {
            $result[] = $this->dumpPrefix . ' instanceof \\' . get_class($node);
            foreach ($node->getSubNodeNames() as $key) {
                $prefix = $this->dumpPrefix . '->' . $key;
                $value = $node->$key;
                if (is_null($value)) {
                    $result[] = $prefix . ' === null';
                } elseif (is_scalar($value)) {
                    if ('flags' === $key || 'newModifier' === $key) {
                        $result[] = $prefix . ' === ' . $this->dumpFlags($value);
                    } elseif ('type' === $key && $node instanceof Include_) {
                        $result[] = $prefix . ' === ' . $this->dumpIncludeType($value);
                    } elseif ('type' === $key
                        && ($node instanceof Use_ || $node instanceof UseUse || $node instanceof GroupUse)) {
                        $result[] = $prefix . ' === ' . $this->dumpUseType($value);
                    } else {
                        $result[] = $prefix . ' === ' . var_export($value, true);
                    }
                } else {
                    $oldPrefix = $this->dumpPrefix;
                    $this->dumpPrefix = $prefix;
                    $result = array_merge($result, [$this->dumpRecursive($value)]);
                    $this->dumpPrefix = $oldPrefix;
                }
            }
        } elseif (is_array($node)) {
            $result[] = 'count(' . $this->dumpPrefix . ') === ' . var_export(count($node), true);
            foreach ($node as $key => $value) {
                $prefix = $this->dumpPrefix . '[' . var_export($key, true) . ']';
                if (is_null($value)) {
                    $result[] = $prefix . ' === null';
                } elseif (is_scalar($value)) {
                    $result[] = $prefix . ' === ' . var_export($value, true);
                } else {
                    $oldPrefix = $this->dumpPrefix;
                    $this->dumpPrefix = $prefix;
                    $result = array_merge($result, [$this->dumpRecursive($value)]);
                    $this->dumpPrefix = $oldPrefix;
                }
            }
        } else {
            throw new \InvalidArgumentException('Can only dump nodes and arrays.');
        }

        return implode("\n&& ", $result);
    }

    protected function dumpFlags($flags)
    {
        $strs = [];
        if ($flags & Class_::MODIFIER_PUBLIC) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC';
        }
        if ($flags & Class_::MODIFIER_PROTECTED) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED';
        }
        if ($flags & Class_::MODIFIER_PRIVATE) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE';
        }
        if ($flags & Class_::MODIFIER_ABSTRACT) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_ABSTRACT';
        }
        if ($flags & Class_::MODIFIER_STATIC) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_STATIC';
        }
        if ($flags & Class_::MODIFIER_FINAL) {
            $strs[] = '\PhpParser\Node\Stmt\Class_::MODIFIER_FINAL';
        }

        if ($strs) {
            return implode(' | ', $strs);
        } else {
            return $flags;
        }
    }

    protected function dumpIncludeType($type)
    {
        $map = [
            Include_::TYPE_INCLUDE => '\PhpParser\Node\Expr\Include_::TYPE_INCLUDE',
            Include_::TYPE_INCLUDE_ONCE => '\PhpParser\Node\Expr\Include_::TYPE_INCLUDE_ONCE',
            Include_::TYPE_REQUIRE => '\PhpParser\Node\Expr\Include_::TYPE_REQUIRE',
            Include_::TYPE_REQUIRE_ONCE => '\PhpParser\Node\Expr\Include_::TYPE_REQUIRE_ONCE',
        ];
        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type];
    }

    protected function dumpUseType($type)
    {
        $map = [
            Use_::TYPE_UNKNOWN => '\PhpParser\Node\Stmt\Use_::TYPE_UNKNOWN',
            Use_::TYPE_NORMAL => '\PhpParser\Node\Stmt\Use_::TYPE_NORMAL',
            Use_::TYPE_FUNCTION => '\PhpParser\Node\Stmt\Use_::TYPE_FUNCTION',
            Use_::TYPE_CONSTANT => '\PhpParser\Node\Stmt\Use_::TYPE_CONSTANT',
        ];
        if (!isset($map[$type])) {
            return $type;
        }
        return $map[$type] . ' (' . $type . ')';
    }
}
