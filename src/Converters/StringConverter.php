<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;
use TypeError;

#[Attribute]
class StringConverter implements ConverterInterface
{
    public function __construct(private array $convertTypes = [])
    {
    }

    public function convert(mixed $fieldValue, PropertyType $type): string
    {
        if ($this->convertTypes && !$type->isOneOf($this->convertTypes)) {
            throw new TypeError('string converter: unexpect type');
        }

        return strval($fieldValue);
    }
}