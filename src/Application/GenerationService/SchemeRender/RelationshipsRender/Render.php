<?php

namespace BBLDN\LaravelDbmlGenerator\Application\GenerationService\SchemeRender\RelationshipsRender;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\Schema;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\ClassItem;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\HasOneItem;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\HasManyItem;
use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\BelongsToItem;

final readonly class Render
{
    /**
     * @param array<string, string> $map
     */
    private function renderHasOneItem(
        array &$map,
        Schema $schema,
        HasOneItem $hasOneItem,
        ClassItem $localClassItem,
    ): void {
        $local = "{$localClassItem->tableName}_$hasOneItem->localKey";

        $related = $hasOneItem->related;
        if (true === key_exists($related, $schema->classItemMap)) {
            $foreignClassItem = $schema->classItemMap[$related];

            $foreign = "{$foreignClassItem->tableName}_$hasOneItem->foreignKey";

            $key1 = "{$local}_$foreign";
            $key2 = "{$foreign}_$local";

            if (true === key_exists($key1, $map) || true === key_exists($key2, $map)) {
                return;
            }

            $map[$key1] = vsprintf(
                format: 'Ref "%s":"%s"."%s" - "%s"."%s" [delete: cascade]',
                values: [
                    $key1, $localClassItem->tableName, $hasOneItem->localKey,
                    $foreignClassItem->tableName, $hasOneItem->foreignKey,
                ]
            );
        }
    }

    /**
     * @param array<string, string> $map
     */
    private function renderHasManyItem(
        array &$map,
        Schema $schema,
        HasManyItem $hasManyItem,
        ClassItem $localClassItem,
    ): void {
        $local = "{$localClassItem->tableName}_$hasManyItem->localKey";

        $related = $hasManyItem->related;
        if (true === key_exists($related, $schema->classItemMap)) {
            $foreignClassItem = $schema->classItemMap[$related];

            $foreign = "{$foreignClassItem->tableName}_$hasManyItem->foreignKey";

            $key1 = "{$local}_$foreign";
            $key2 = "{$foreign}_$local";

            if (true === key_exists($key1, $map) || true === key_exists($key2, $map)) {
                return;
            }

            $map[$key1] = vsprintf(
                format: 'Ref "%s":"%s"."%s" < "%s"."%s" [delete: cascade]',
                values: [
                    $key1, $localClassItem->tableName, $hasManyItem->localKey,
                    $foreignClassItem->tableName, $hasManyItem->foreignKey,
                ]
            );
        }
    }

    /**
     * @param array<string, string> $map
     */
    private function renderBelongsToItem(
        array &$map,
        Schema $schema,
        ClassItem $localClassItem,
        BelongsToItem $belongsToItem,
    ): void {
        $local = "{$localClassItem->tableName}_$belongsToItem->foreignKey";

        $related = $belongsToItem->related;
        if (true === key_exists($related, $schema->classItemMap)) {
            $foreignClassItem = $schema->classItemMap[$related];

            $foreign = "{$foreignClassItem->tableName}_$belongsToItem->ownerKey";

            $key1 = "{$local}_$foreign";
            $key2 = "{$foreign}_$local";

            if (true === key_exists($key1, $map) || true === key_exists($key2, $map)) {
                return;
            }

            $map[$key1] = vsprintf(
                format: 'Ref "%s":"%s"."%s" > "%s"."%s" [delete: cascade]',
                values: [
                    $key1, $localClassItem->tableName, $belongsToItem->foreignKey,
                    $foreignClassItem->tableName, $belongsToItem->ownerKey,
                ]
            );
        }
    }

    public function render(Schema $schema): string
    {
        $map = [];
        foreach ($schema->classItemMap as $localClassItem) {
            foreach ($localClassItem->relationshipList as $relationship) {
                switch (get_class($relationship)) {
                    case HasOneItem::class:
                        $this->renderHasOneItem(
                            map: $map,
                            schema: $schema,
                            hasOneItem: $relationship,
                            localClassItem: $localClassItem,
                        );
                        break;
                    case HasManyItem::class:
                        $this->renderHasManyItem(
                            map: $map,
                            schema: $schema,
                            hasManyItem: $relationship,
                            localClassItem: $localClassItem,
                        );
                        break;
                    case BelongsToItem::class:
                        $this->renderBelongsToItem(
                            map: $map,
                            schema: $schema,
                            belongsToItem: $relationship,
                            localClassItem: $localClassItem,
                        );
                        break;
                }
            }
        }

        return implode(PHP_EOL, $map);
    }
}
