<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship;

final class HasOneItem implements RelationshipItem
{
    public function __construct(
        public string $name,
        public string $related = '',
        public string $localKey = '',
        public string $foreignKey = '',
    ) {
    }
}
