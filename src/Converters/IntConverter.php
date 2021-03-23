<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;
use TypeError;

#[Attribute]
class IntConverter implements ConverterInterface
{
    public function __construct(private array $convertTypes = [])
    {
    }

    public function convert(mixed $fieldValue, PropertyType $type): int
    {
        if ($this->convertTypes && !$type->isOneOf($this->convertTypes)) {
            throw new TypeError('int converter: unexpect type');
        }

        return intval($fieldValue);
    }
}