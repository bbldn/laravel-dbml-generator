<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\DTO;

final class EnumItem
{
    public string $name;

    /** @var list<string> */
    public array $valueList = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
