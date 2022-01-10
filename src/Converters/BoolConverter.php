<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\Property;
use Archman\Velcro\TypeHelper;
use Attribute;
use TypeError;

#[Attribute]
class BoolConverter implements ConverterInterface
{
    private Property $boundProperty;

    public function __construct(private array $expectTypes = [])
    {
    }

    public function bindToProperty(Property $property)
    {
        $this->boundProperty = $property;
    }

    public function convert(mixed $fieldValue): bool
    {
        if (is_bool($fieldValue)) {
            return $fieldValue;
        }

        if ($this->expectTypes) {
            [$valueType, $isValueObject] = TypeHelper::getValueType($fieldValue);
            if (!in_array($valueType, $this->expectTypes) &&
                (!$isValueObject || !in_array('object', $this->expectTypes))
            ) {
                throw new TypeError("unexpect type of the value: {$valueType}");
            }
        }

        return boolval($fieldValue);
    }
}