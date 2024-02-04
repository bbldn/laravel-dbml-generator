<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO;

use BBLDN\LaravelDbmlGenerator\Domain\DTO\Relationship\RelationshipItem;

final class ClassItem
{
    public string $name;

    /** @var array<string, PropertyItem> */
    public array $itemMap = [];

    public PrimaryKey $primaryKey;

    public string $tableName = '';

    /** @var list<RelationshipItem> */
    public array $relationshipList = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->primaryKey = new PrimaryKey(
            type: 'integer',
            properties: ['id'],
            autoincrement: true,
        );
    }
}
