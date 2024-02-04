<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\FillableFiller;

use RuntimeException;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\PropertyItem;

final readonly class Filler
{
    public function fill(ClassItem $classItem, ReflectionClass $reflectionClass): void
    {
        $property = $reflectionClass->getProperty('fillable');
        if (null !== $property) {
            $propertyDefaultValue = $property->getDefaultValue();
            if (false === is_array($propertyDefaultValue)) {
                throw new RuntimeException(
                    sprintf('[%s->fillable] must be array or null', $reflectionClass->getName())
                );
            }

            foreach ($propertyDefaultValue as $propertyName) {
                if (false === is_string($propertyName)) {
                    throw new RuntimeException(
                        sprintf('[%s->fillable] should only contain strings', $reflectionClass->getName())
                    );
                }

                if (false === key_exists($propertyName, $classItem->itemMap)) {
                    $classItem->itemMap[$propertyName] = new PropertyItem(
                        type: 'string',
                        name: $propertyName,
                    );
                }
            }
        }
    }
}
