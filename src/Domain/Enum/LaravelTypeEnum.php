<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\Enum;

final readonly class LaravelTypeEnum
{
    public const MODEL = 'Illuminate\Database\Eloquent\Model';

    public const PIVOT = 'Illuminate\Database\Eloquent\Relations\Pivot';

    public const HAS_ONE = 'Illuminate\Database\Eloquent\Relations\HasOne';

    public const HAS_MANY = 'Illuminate\Database\Eloquent\Relations\HasMany';

    public const BELONGS_TO = 'Illuminate\Database\Eloquent\Relations\BelongsTo';

    private function __construct()
    {
    }
}
