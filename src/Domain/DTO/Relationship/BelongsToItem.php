<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship;

final class BelongsToItem implements RelationshipItem
{
    public function __construct(
        public string $name,
        public string $related = '',
        public string $ownerKey = '',
        public string $foreignKey = '',
    ) {
    }
}
