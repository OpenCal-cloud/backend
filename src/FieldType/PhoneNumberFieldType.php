<?php

declare(strict_types=1);

namespace App\FieldType;

class PhoneNumberFieldType extends AbstractFieldType
{
    public const string FIELD_TYPE_NAME = 'phone_type';

    public static function getIdentifier(): string
    {
        return self::FIELD_TYPE_NAME;
    }
}
