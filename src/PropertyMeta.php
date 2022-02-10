<?php

declare(strict_types=1);

namespace Archman\Velcro;

use Archman\Velcro\Converters\ConverterInterface;

class PropertyMeta
{
    public function __construct(
        private string              $propertyName,
        private string              $fieldName,
        private Property            $property,
        private ?ConverterInterface $converter,
        private ?\Closure           $setter,
        private bool                $legacyReadonly,
    ) {
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getConverter(): ?ConverterInterface
    {
        return $this->converter;
    }

    public function getSetter(): ?\Closure
    {
        return $this->setter;
    }

    public function isLegacyReadonly(): bool
    {
        return $this->legacyReadonly;
    }
}