<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;
use TypeError;

#[Attribute]
class FloatConverter implements ConverterInterface
{
    public function __construct(private array $convertTypes = [])
    {
    }

    public function convert(mixed $fieldValue, PropertyType $type): float
    {
        if ($this->convertTypes && !$type->isOneOf($this->convertTypes)) {
            throw new TypeError('float converter: unexpect type');
        }

        return floatval($fieldValue);
    }
}