<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\KeyTypeFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Domain\Enum\SupportedTypeEnum;

final readonly class Filler
{
    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('keyType');
        if (null === $property) {
            throw new RuntimeException(
                sprintf('[%s] should contain $keyType property', $reflectionClass->getName())
            );
        }

        $propertyDefaultValue = $property->getDefaultValue();
        if (false === is_string($propertyDefaultValue)) {
            throw new RuntimeException(
                sprintf('[%s->keyType] must be string', $reflectionClass->getName())
            );
        }

        if (SupportedTypeEnum::INT !== $propertyDefaultValue) {
            if (SupportedTypeEnum::STRING !== $propertyDefaultValue) {
                throw new RuntimeException(
                    sprintf('[%s->keyType] must be equal to string or int', $reflectionClass->getName())
                );
            }
        }

        $classItem->primaryKey->type = $propertyDefaultValue;
    }
}
