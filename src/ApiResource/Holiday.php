<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use App\State\HolidaysStateProvider;
use Safe\DateTimeImmutable;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    operations: [
        new GetCollection(
            provider: HolidaysStateProvider::class,
            parameters: [
                'country' => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The parameter "country" is required.',
                        ]),
                    ],
                ),
                'year'    => new QueryParameter(
                    constraints: [
                        new NotBlank([
                            'message' => 'The parameter "year" is required.',
                        ]),
                    ],
                ),
            ],
        ),
    ],
    security: "is_granted('IS_AUTHENTICATED_FULLY')",
)]
class Holiday
{
    private string $country;

    private int $year;

    private DateTimeImmutable $date;

    private string $localName;

    private string $name;

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocalName(): string
    {
        return $this->localName;
    }

    public function setLocalName(string $localName): self
    {
        $this->localName = $localName;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
