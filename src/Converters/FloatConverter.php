<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Archman\DataModel\TypeHelper;
use Attribute;
use TypeError;

#[Attribute]
class FloatConverter implements ConverterInterface
{
    public function __construct(private array $expectTypes = [])
    {
    }

    public function convert(mixed $fieldValue, PropertyType $type): float
    {
        if (is_float($fieldValue)) {
            return $fieldValue;
        }

        if ($this->expectTypes) {
            [$valueType, $isValueObject] = TypeHelper::getValueType($fieldValue);
            if (!in_array($valueType, $this->expectTypes) &&
                (!$isValueObject || !in_array('object', $this->expectTypes))
            ) {
                throw new TypeError('float converter: unexpect type');
            }
        }

        return floatval($fieldValue);
    }
}