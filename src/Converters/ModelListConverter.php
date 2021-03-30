<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\DataModel;
use Archman\Velcro\Property;
use Attribute;
use TypeError;

#[Attribute]
class ModelListConverter implements ConverterInterface
{
    public function __construct(private string $modelClass)
    {
        if (!is_subclass_of($this->modelClass, DataModel::class)) {
            throw new TypeError("{$this->modelClass} is not a subclass of DataModel");
        }
    }

    public function convert(mixed $fieldValue, Property $property): array
    {
        if (!is_array($fieldValue)) {
            throw new TypeError("value of {$property->getBoundFieldName()} should be a type of array");
        }

        $result = [];
        foreach ($fieldValue as $each) {
            if (!is_array($each)) {
                throw new TypeError("the element of {$property->getBoundFieldName()} should be a type of array");
            }

            $result[] = new $this->modelClass($each);
        }

        return $result;
    }
}