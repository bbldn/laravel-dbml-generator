<?php

namespace BBLDN\LaravelDbmlGenerator\Domain\Enum;

enum SupportedRelationshipEnum: string
{
    case HAS_ONE = 'hasOne';

    case HAS_MANY = 'hasMany';

    case BELONGS_TO = 'belongsTo';
}
