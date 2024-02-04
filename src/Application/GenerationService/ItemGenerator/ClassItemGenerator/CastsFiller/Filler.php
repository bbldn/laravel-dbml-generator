<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\CastsFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\PropertyItem;

final readonly class Filler
{
    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('casts');
        if (null !== $property) {
            $propertyDefaultValue = $property->getDefaultValue();
            if (false === is_array($propertyDefaultValue)) {
                throw new RuntimeException(
                    sprintf('[%s->casts] must be array or null', $reflectionClass->getName())
                );
            }

            foreach ($propertyDefaultValue as $propertyName => $propertyType) {
                if (false === is_string($propertyName)) {
                    throw new RuntimeException(
                        sprintf('[%s->casts] key should only contain strings', $reflectionClass->getName())
                    );
                }

                if (false === is_string($propertyType)) {
                    throw new RuntimeException(
                        sprintf('[%s->casts] value should only contain strings', $reflectionClass->getName())
                    );
                }

                $classItem->itemMap[$propertyName] = new PropertyItem(
                    name: $propertyName,
                    type: $propertyType,
                );
            }
        }
    }
}
