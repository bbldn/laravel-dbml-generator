<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\RelationshipsFiller;

use RuntimeException;
use PhpParser\Node\Arg;
use Roave\BetterReflection\BetterReflection;
use PhpParser\Node\Identifier as IdentifierNode;
use PhpParser\Node\Scalar\String_ as StringNode;
use PhpParser\Node\Stmt\ClassMethod as MethodNode;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use PhpParser\Node\Expr\MethodCall as MethodCallExpr;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\LaravelTypeEnum;
use BBLDN\LaravelDbmlGenerator\Application\Helper\AstHelper;
use PhpParser\Node\Expr\ClassConstFetch as ClassConstFetchExpr;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\HasOneItem;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\HasManyItem;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\BelongsToItem;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\SupportedRelationshipEnum;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\RelationshipItem;

final readonly class Filler
{
    public function __construct(
        private BetterReflection $betterReflection
    ) {
    }

    private function parseRelationship(
        string $methodName,
        ReflectionClass $reflectionClass,
        SupportedRelationshipEnum $relationshipType,
    ): RelationshipItem|null {
        $phpParser = $this->betterReflection->phpParser();

        $ast = $phpParser->parse(
            code: $reflectionClass->getLocatedSource()->getSource(),
        );

        if (null === $ast) {
            return null;
        }

        $result = match ($relationshipType) {
            SupportedRelationshipEnum::HAS_ONE => new HasOneItem(name: $methodName),
            SupportedRelationshipEnum::HAS_MANY => new HasManyItem(name: $methodName),
            SupportedRelationshipEnum::BELONGS_TO => new BelongsToItem(name: $methodName),
        };

        $parameters = match ($relationshipType) {
            SupportedRelationshipEnum::BELONGS_TO => [
                0 => 'related',
                1 => 'foreignKey',
                2 => 'ownerKey',
            ],
            SupportedRelationshipEnum::HAS_ONE, SupportedRelationshipEnum::HAS_MANY => [
                0 => 'related',
                1 => 'foreignKey',
                2 => 'localKey',
            ],
        };

        /** @var MethodNode|null $method */
        $method = AstHelper::find($ast, "namespace class method::$methodName");
        if (null !== $method) {
            $stmts = $method->stmts;
            if (null !== $stmts) {
                /** @var MethodCallExpr|null $methodCall */
                $methodCall = AstHelper::find($stmts, "return methodCall::$relationshipType->value");

                foreach ($methodCall->args as $index => $arg) {
                    if (true === is_a($arg, Arg::class)) {
                        $field = null;
                        $name = $arg->name;
                        switch ($index) {
                            case 0:
                                if (true === is_a($name, IdentifierNode::class)) {
                                    $field = AstHelper::getIdentifierNodeValue($name);
                                }
                                break;
                            case 1:
                                if (true === is_a($name, IdentifierNode::class)) {
                                    $field = AstHelper::getIdentifierNodeValue($name);
                                }
                                break;
                            case 2:
                                if (true === is_a($name, IdentifierNode::class)) {
                                    $field = AstHelper::getIdentifierNodeValue($name);
                                }
                                break;
                            default:
                                throw new RuntimeException("'$relationshipType->value' must have three arguments");
                        }

                        if (null === $field) {
                            $field = $parameters[$index];
                        }

                        $value = $arg->value;
                        switch (true) {
                            case is_a($value, StringNode::class):
                                $tmp = AstHelper::getStringNodeValue($value);
                                if (null !== $tmp) {
                                    $result->$field = $tmp;
                                }
                                break;
                            case is_a($value, ClassConstFetchExpr::class):
                                $tmp = AstHelper::getClassConstFetchExprValue($value);
                                if (null !== $tmp) {
                                    $result->$field = AstHelper::revolveFCN($ast, $tmp);
                                }
                                break;
                        }
                    }
                }
            }
        }

        foreach ($parameters as $parameter) {
            $value = (string)$result->$parameter;
            if (0 === mb_strlen($value)) {
                return null;
            }
        }

        return $result;
    }

    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        foreach ($reflectionClass->getMethods() as $method) {
            if (0 === $method->getNumberOfParameters()) {
                $returnType = $method->getReturnType();
                if (null !== $returnType) {
                    switch ((string)$returnType) {
                        case LaravelTypeEnum::HAS_ONE:
                            $relationshipItem = $this->parseRelationship(
                                methodName: $method->getName(),
                                reflectionClass: $reflectionClass,
                                relationshipType: SupportedRelationshipEnum::HAS_ONE,
                            );

                            if (null !== $relationshipItem) {
                                $classItem->relationshipList[] = $relationshipItem;
                            }
                            break;
                        case LaravelTypeEnum::HAS_MANY:
                            $relationshipItem = $this->parseRelationship(
                                methodName: $method->getName(),
                                reflectionClass: $reflectionClass,
                                relationshipType: SupportedRelationshipEnum::HAS_MANY,
                            );

                            if (null !== $relationshipItem) {
                                $classItem->relationshipList[] = $relationshipItem;
                            }
                            break;
                        case LaravelTypeEnum::BELONGS_TO:
                            $relationshipItem = $this->parseRelationship(
                                methodName: $method->getName(),
                                reflectionClass: $reflectionClass,
                                relationshipType: SupportedRelationshipEnum::BELONGS_TO,
                            );

                            if (null !== $relationshipItem) {
                                $classItem->relationshipList[] = $relationshipItem;
                            }
                            break;
                    }
                }
            }
        }
    }
}
