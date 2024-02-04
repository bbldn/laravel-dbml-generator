<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\PrimaryKeyFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\LaravelTypeEnum;

final readonly class Filler
{
    private function fillModel(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('primaryKey');
        if (null === $property) {
            throw new RuntimeException(
                sprintf('[%s] should contain $primaryKey property', $reflectionClass->getName())
            );
        }

        $defaultValue = $property->getDefaultValue();
        if (false === is_string($defaultValue)) {
            throw new RuntimeException(
                sprintf('[%s->primaryKey] must be string', $reflectionClass->getName())
            );
        }

        $classItem->primaryKey->properties = [$defaultValue];
    }

    private function fillPivot(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        /* foreignKey | Start */
        $propertyForeignKey = $reflectionClass->getProperty('foreignKey');
        if (null === $propertyForeignKey) {
            throw new RuntimeException(
                sprintf('[%s] should contain $foreignKey property', $reflectionClass->getName())
            );
        }

        $defaultValueForeignKey = $propertyForeignKey->getDefaultValue();
        if (false === is_string($defaultValueForeignKey)) {
            throw new RuntimeException(
                sprintf('[%s->foreignKey] must be string', $reflectionClass->getName())
            );
        }
        /* foreignKey | End */

        /* relatedKey | Start */
        $propertyRelatedKey = $reflectionClass->getProperty('relatedKey');
        if (null === $propertyRelatedKey) {
            throw new RuntimeException(
                sprintf('[%s] should contain $relatedKey property', $reflectionClass->getName())
            );
        }

        $defaultValueRelatedKey = $propertyRelatedKey->getDefaultValue();
        if (false === is_string($defaultValueRelatedKey)) {
            throw new RuntimeException(
                sprintf('[%s->relatedKey] must be string', $reflectionClass->getName())
            );
        }
        /* relatedKey | End */

        $classItem->primaryKey->properties = [$defaultValueForeignKey, $defaultValueRelatedKey];
    }

    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        if (false === $reflectionClass->isSubclassOf(LaravelTypeEnum::PIVOT)) {
            $this->fillModel($classItem, $reflectionClass);
        } else {
            $this->fillPivot($classItem, $reflectionClass);
        }
    }
}
