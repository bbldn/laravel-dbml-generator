<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO;

final class Schema
{
    /**
     * @param array<string, EnumItem> $enumItemMap
     * @param array<string, ClassItem> $classItemMap
     */
    public function __construct(
        public array $enumItemMap,
        public array $classItemMap,
    ) {
    }
}
