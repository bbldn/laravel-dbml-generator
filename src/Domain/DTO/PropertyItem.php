<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO;

final class PropertyItem
{
    public function __construct(
        public string $name,
        public string $type,
    ) {
    }
}
