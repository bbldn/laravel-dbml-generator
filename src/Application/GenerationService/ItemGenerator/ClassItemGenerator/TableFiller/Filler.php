<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\TableFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;

final readonly class Filler
{
    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('table');
        if (null === $property) {
            throw new RuntimeException(
                sprintf('[%s] should contain $table property', $reflectionClass->getName())
            );
        }

        $propertyDefaultValue = $property->getDefaultValue();
        if (true === is_string($propertyDefaultValue)) {
            $classItem->tableName = $propertyDefaultValue;
        }
    }
}
