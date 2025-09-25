<?php

declare(strict_types=1);

namespace App\MeetingProvider;

use App\Entity\Event;
use App\FieldType\CountryFieldType;
use App\FieldType\PhoneNumberFieldType;

class PhoneMeetingProvider extends AbstractMeetingProvider
{
    public const string PROVIDER_IDENTIFIER = 'phone';

    public function getIdentifier(): string
    {
        return 'phone';
    }

    public function getName(): string
    {
        return 'Phone call';
    }

    public function generateLocation(Event $event): string
    {
        $fieldValues = $event->getAdditionalEventFieldValues();

        $data = [];

        foreach ($fieldValues as $fieldValue) {
            $data[$fieldValue->getField()->getFieldType()] = $fieldValue->getValue();
        }

        return \sprintf(
            '%s %s',
            $data[CountryFieldType::FIELD_TYPE_NAME],
            $data[PhoneNumberFieldType::FIELD_TYPE_NAME],
        );
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function getRequiredFields(): array
    {
        return [
            'phone_number' => [
                'type' => PhoneNumberFieldType::FIELD_TYPE_NAME,
            ],
            'country'      => [
                'type' => CountryFieldType::FIELD_TYPE_NAME,
            ],
        ];
    }
}
