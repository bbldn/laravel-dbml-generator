<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\IncrementingFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;

final readonly class Filler
{
    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('incrementing');
        if (null === $property) {
            throw new RuntimeException(
                sprintf('[%s] should contain $incrementing property', $reflectionClass->getName())
            );
        }

        $propertyDefaultValue = $property->getDefaultValue();
        if (false === is_bool($propertyDefaultValue)) {
            throw new RuntimeException(
                sprintf('[%s->incrementing] should only contain boolean', $reflectionClass->getName())
            );
        }

        $classItem->primaryKey->autoincrement = $propertyDefaultValue;
    }
}
