<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\EnumItemGenerator;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\EnumItem;
use Roave\BetterReflection\Reflection\ReflectionEnum;

final readonly class Generator
{
    public function generate(ReflectionEnum $reflectionEnum): EnumItem|null
    {
        $enumItem = new EnumItem($reflectionEnum->getName());

        $valueMap = [];
        foreach ($reflectionEnum->getCases() as $reflectionEnumCase) {
            $value = (string)$reflectionEnumCase->getValue();
            if (false === key_exists($value, $valueMap)) {
                $valueMap[$value] = $value;
            }
        }

        $enumItem->valueList = array_values($valueMap);

        return $enumItem;
    }
}
