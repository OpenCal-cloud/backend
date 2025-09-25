<?php

declare(strict_types=1);

namespace App\FieldType;

class FieldTypesService
{
    private array $fieldTypes = [
        PhoneNumberFieldType::FIELD_TYPE_NAME,
    ];

    public function getByIdentifier(string $identifier): AbstractFieldType
    {
        if (isset($this->fieldTypes[$identifier])) {
            return $this->fieldTypes[$identifier];
        }

        throw new \RuntimeException(\sprintf(
            'Field-type %s is not implemented.',
            $identifier,
        ));
    }
}
