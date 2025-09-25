<?php

declare(strict_types=1);

namespace App\FieldType;

abstract class AbstractFieldType
{
    abstract public static function getIdentifier(): string;
}
