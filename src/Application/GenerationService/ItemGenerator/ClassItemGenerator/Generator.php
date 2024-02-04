<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator;

use Roave\BetterReflection\BetterReflection;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use Roave\BetterReflection\Reflection\ReflectionClass;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\CastsFiller\Filler as CastsFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\TableFiller\Filler as TableFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\KeyTypeFiller\Filler as KeyTypeFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\FillableFiller\Filler as FillableFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\PrimaryKeyFiller\Filler as PrimaryKeyFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\IncrementingFiller\Filler as IncrementingFiller;
use BBLDN\LaravelDbmlGenerator\Application\GenerationService\ItemGenerator\ClassItemGenerator\RelationshipsFiller\Filler as RelationshipsFiller;

final readonly class Generator
{
    private CastsFiller $castsFiller;

    private TableFiller $tableFiller;

    private KeyTypeFiller $keyTypeFiller;

    private FillableFiller $fillableFiller;

    private PrimaryKeyFiller $primaryKeyFiller;

    private IncrementingFiller $incrementingFiller;

    private RelationshipsFiller $relationshipsFiller;

    public function __construct(BetterReflection $betterReflection)
    {
        $this->castsFiller = new CastsFiller();
        $this->tableFiller = new TableFiller();
        $this->keyTypeFiller = new KeyTypeFiller();
        $this->fillableFiller = new FillableFiller();
        $this->primaryKeyFiller = new PrimaryKeyFiller();
        $this->incrementingFiller = new IncrementingFiller();
        $this->relationshipsFiller = new RelationshipsFiller(
            betterReflection: $betterReflection,
        );
    }

    public function generate(ReflectionClass $reflectionClass): ClassItem|null
    {
        $classItem = new ClassItem($reflectionClass->getName());

        $this->tableFiller->fill($classItem, $reflectionClass);
        if (0 === mb_strlen($classItem->tableName)) {
            return null;
        }

        $this->castsFiller->fill($classItem, $reflectionClass);
        $this->keyTypeFiller->fill($classItem, $reflectionClass);
        $this->fillableFiller->fill($classItem, $reflectionClass);
        $this->primaryKeyFiller->fill($classItem, $reflectionClass);
        $this->incrementingFiller->fill($classItem, $reflectionClass);
        $this->relationshipsFiller->fill($classItem, $reflectionClass);

        return $classItem;
    }
}
