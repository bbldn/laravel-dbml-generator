<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO;

final class PrimaryKey
{
    /**
     * @param list<string> $properties
     */
    public function __construct(
        public string $type,
        public array $properties,
        public bool $autoincrement,
    ) {
    }
}
