<?php

namespace BBLDN\LaravelDbmlGenerator\Application\Helper;

use stdClass;
use RuntimeException;
use PhpParser\Node\Stmt;
use PhpParser\NodeAbstract as Node;
use PhpParser\Node\Name as NameNode;
use PhpParser\Node\Stmt\Use_ as UseNode;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Return_ as ReturnNode;
use PhpParser\Node\Identifier as IdentifierNode;
use PhpParser\Node\Scalar\String_ as StringNode;
use PhpParser\Node\Expr\Variable as VariableExpr;
use PhpParser\Node\Stmt\ClassMethod as MethodNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\Node\Expr\MethodCall as MethodCallExpr;
use PhpParser\Node\Expr\ClassConstFetch as ClassConstFetchExpr;

final readonly class AstHelper
{
    public static function getNameNodeValue(NameNode $expr): string|null
    {
        return implode('\\', $expr->getParts());
    }

    public static function getStringNodeValue(StringNode $expr): string|null
    {
        return $expr->value;
    }

    public static function getIdentifierNodeValue(IdentifierNode $expr): string|null
    {
        return $expr->name;
    }

    public static function getClassConstFetchExprValue(ClassConstFetchExpr $expr): string|null
    {
        $classNodeName = $expr->class;
        if (true === is_a($classNodeName, NameNode::class)) {
            return self::getNameNodeValue($classNodeName);
        }

        return null;
    }

    public static function revolveFCN(array $ast, string $className): string
    {
        if (true === key_exists(0, $ast)) {
            $namespaceNode = $ast[0];
            if (true === is_a($namespaceNode, NamespaceNode::class)) {
                foreach ($namespaceNode->stmts as $useNode) {
                    if (true === is_a($useNode, UseNode::class)) {
                        foreach ($useNode->uses as $useUseNode) {
                            $alias = $useUseNode->alias;
                            if (null !== $alias) {
                                $tmp = self::getIdentifierNodeValue($alias);
                                if ($tmp === $className) {
                                    return self::getNameNodeValue($useUseNode->name);
                                }
                            }

                            $parts = $useUseNode->name->getParts();
                            if (count($parts) > 0) {
                                $tmp = $parts[count($parts) - 1];
                                if ($tmp === $className) {
                                    return self::getNameNodeValue($useUseNode->name);
                                }
                            }
                        }
                    }
                }

                $nameNode = $namespaceNode->name;
                if (null !== $nameNode) {
                    return self::getNameNodeValue($nameNode) . "\\$className";
                }
            }
        }

        return $className;
    }

    /**
     * @param array<array-key, Stmt> $ast
     * @return Node|null
     */
    public static function find(array $ast, string $path): Node|null
    {
        /* Normalize Path | Start */
        $normalizedPath = explode(' ', $path);
        foreach ($normalizedPath as $index => $item) {
            $itemList = explode('::', $item);
            $itemList[0] = match ($itemList[0]) {
                'class' => ClassNode::class,
                'return' => ReturnNode::class,
                'method' => MethodNode::class,
                'variable' => VariableExpr::class,
                'namespace' => NamespaceNode::class,
                'methodCall' => MethodCallExpr::class,
                default => throw new RuntimeException("Unsupported node type: $itemList[0]"),
            };

            $normalizedPath[$index] = $itemList;
        }
        /* Normalize Path | End */

        $stdClass = new stdClass();
        $stdClass->stmts = $ast;
        foreach ($normalizedPath as $pathList) {
            $found = false;

            switch (true) {
                case property_exists($stdClass, 'stmts'):
                    $nodeList = $stdClass->stmts;
                    break;
                case property_exists($stdClass, 'expr'):
                    $nodeList = $stdClass->expr;
                    if (false === is_array($nodeList)) {
                        $nodeList = [$nodeList];
                    }
                    break;
                default:
                    $nodeList = [];
                    break;
            }

            foreach ($nodeList as $node) {
                if (true === is_a($node, $pathList[0])) {
                    if (true === key_exists(1, $pathList)) {
                        if (true === property_exists($node, 'name')) {
                            $name = $node->name;
                            if (null === $name) {
                                continue;
                            }

                            if (true === is_string($name)) {
                                if ($name !== $pathList[1]) {
                                    continue;
                                }
                            }

                            if (true === is_a($name, IdentifierNode::class)) {
                                if ($name->name !== $pathList[1]) {
                                    continue;
                                }
                            }
                        } else {
                            continue;
                        }
                    }

                    $stdClass = $node;
                    $found = true;
                    break;
                }
            }

            if (false === $found) {
                return null;
            }
        }

        return $stdClass;
    }

    private function __construct()
    {
    }
}
