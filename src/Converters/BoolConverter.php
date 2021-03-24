<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;
use TypeError;

#[Attribute]
class BoolConverter implements ConverterInterface
{
    public function __construct(private array $convertTypes = [])
    {
    }

    public function convert(mixed $fieldValue, PropertyType $type): bool
    {
        if ($this->convertTypes && !$type->isOneOf($this->convertTypes)) {
            throw new TypeError('bool converter: unexpect type');
        }

        return boolval($fieldValue);
    }
}