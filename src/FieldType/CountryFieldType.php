<?php

declare(strict_types=1);

namespace App\FieldType;

class CountryFieldType extends AbstractFieldType
{
    public const string FIELD_TYPE_NAME = 'country';

    public static function getIdentifier(): string
    {
        return self::FIELD_TYPE_NAME;
    }
}
