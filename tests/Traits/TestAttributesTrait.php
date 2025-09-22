<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use ApiPlatform\Metadata\ApiResource;
use Spatie\Snapshots\MatchesSnapshots;
use function Safe\json_encode;

trait TestAttributesTrait
{
    use MatchesSnapshots;

    public function testClassAttributes(): void
    {
        $data = [];

        $attributes = $this->getAttributesForClass();

        foreach ($attributes as $attribute) {
            $arguments = $attribute->getArguments();

            if (0 === \count($arguments)) {
                continue;
            }

            if ($this->isApiPlatformInstalled() && ApiResource::class === $attribute->getName()) {
                /** @var \ReflectionAttribute<ApiResource> $argument */
                foreach ($arguments as $key => $argument) {
                    /** @phpstan-ignore-next-line */
                    if (!\is_array($argument)) {
                        $data[$attribute->getName()][$key] = $argument;
                    } else {
                        foreach ($argument as $item) {
                            /** @phpstan-ignore-next-line  */
                            if (\is_object($item)) {
                                /** @phpstan-ignore-next-line  */
                                $refClass   = new \ReflectionClass($item);
                                $propValues = $refClass->getProperties();

                                /** @phpstan-ignore-next-line */
                                $data[$attribute->getName()][$item::class] = [];

                                foreach ($propValues as $property) {
                                    if (null === $property->getValue($item)) {
                                        continue;
                                    }

                                    /** @phpstan-ignore-next-line */
                                    $data[$attribute->getName()][$item::class][$property->getName()]
                                        = \serialize($property->getValue($item));
                                }
                            } else {
                                /** @phpstan-ignore-next-line */
                                $data[$attribute->getName()][$key][] = $argument;
                            }
                        }
                    }
                }
            } else {
                $data[$attribute->getName()] = $arguments;
            }
        }

        $json = json_encode($data);
        $this->assertMatchesJsonSnapshot($json);
    }

    public function testPropertyAttributes(): void
    {
        $propArray = [];

        $props = $this->getProperties();

        /** @var \ReflectionProperty $prop */
        foreach ($props as $prop) {
            $attributes = $prop->getAttributes();

            foreach ($attributes as $attribute) {
                $arguments = $attribute->getArguments();

                if (0 === \count($arguments)) {
                    continue;
                }

                $propArray[$prop->getName()][$attribute->getName()] = $arguments;
            }
        }

        $json = json_encode($propArray);
        $this->assertMatchesJsonSnapshot($json);
    }

    /** @phpstan-ignore-next-line */
    protected function getReflectionClass(): \ReflectionClass
    {
        return new \ReflectionClass($this->getInstance());
    }

    /**
     * @return array<\ReflectionAttribute>
     *
     * @phpstan-ignore-next-line
     */
    protected function getAttributes(): array
    {
        return $this->getReflectionClass()->getAttributes();
    }

    /**
     * @return array<\ReflectionAttribute>
     *
     * @phpstan-ignore-next-line
     */
    protected function getAttributesForClass(): array
    {
        return $this->getAttributes();
    }

    /**
     * @return array<\ReflectionProperty>
     *
     * @phpstan-ignore-next-line
     */
    protected function getProperties(): array
    {
        return $this->getReflectionClass()->getProperties();
    }

    abstract protected function getInstance(): object;

    private function isApiPlatformInstalled(): bool
    {
        return \class_exists(ApiResource::class);
    }
}
