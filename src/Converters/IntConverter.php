<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Archman\DataModel\TypeHelper;
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
        if (is_int($fieldValue)) {
            return $fieldValue;
        }

        if ($this->convertTypes) {
            [$valueType, $isValueObject] = TypeHelper::getValueType($fieldValue);
            if (!in_array($valueType, $this->convertTypes) &&
                (!$isValueObject || !in_array('object', $this->convertTypes))
            ) {
                throw new TypeError('int converter: unexpect type');
            }
        }

        return intval($fieldValue);
    }
}