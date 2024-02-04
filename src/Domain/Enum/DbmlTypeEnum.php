<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\Enum;

final readonly class DbmlTypeEnum
{
    public const INT = 'int';

    public const BOOL = 'bool';

    public const DATE = 'date';

    public const ENUM = 'enum';

    public const TEXT = 'text';

    public const DECIMAL = 'decimal';

    public const DATETIME = 'datetime';

    /**
     * @param array<array-key, string> $values
     */
    public static function ENUM(array $values): string
    {
        $result = self::ENUM;
        if (count($values) > 0) {
            $result .= '(' . implode(',', $values) . ')';
        }

        return $result;
    }

    private function __construct()
    {
    }
}
