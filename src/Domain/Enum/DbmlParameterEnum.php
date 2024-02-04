<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\Enum;

final readonly class DbmlParameterEnum
{
    public const PRIMARY_KEY = 'pk';

    public const NOT_NULL = 'not null';

    public const INCREMENT = 'increment';

    private function __construct()
    {
    }
}
