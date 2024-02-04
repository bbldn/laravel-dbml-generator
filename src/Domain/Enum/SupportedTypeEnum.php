<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\Enum;

final readonly class SupportedTypeEnum
{
    public const INT = 'int';

    public const BOOL = 'bool';

    public const DATE = 'date';

    public const FLOAT = 'float';

    public const DOUBLE = 'double';

    public const HASHED = 'hashed';

    public const STRING = 'string';

    public const BOOLEAN = 'boolean';

    public const INTEGER = 'integer';

    public const DATETIME = 'datetime';

    public const TIMESTAMP = 'timestamp';

    public const IMMUTABLE_DATE = 'immutable_date';

    public const IMMUTABLE_DATETIME = 'immutable_datetime';

    private function __construct()
    {
    }
}
