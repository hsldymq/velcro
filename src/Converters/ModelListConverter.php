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
    private Property $boundProperty;

    public function __construct(private string $modelClass)
    {
        if (!is_subclass_of($this->modelClass, DataModel::class)) {
            throw new TypeError("{$this->modelClass} is not a subclass of DataModel");
        }
    }

    public function bindToProperty(Property $property)
    {
        $this->boundProperty = $property;
    }

    public function convert(mixed $fieldValue): array
    {
        if (!is_array($fieldValue)) {
            throw new TypeError("value of {$this->boundProperty->getBoundFieldName()} should be a type of array");
        }

        $result = [];
        foreach ($fieldValue as $key => $each) {
            if (!is_array($each)) {
                throw new TypeError("the element of {$this->boundProperty->getBoundFieldName()} should be a type of array");
            }

            $result[$key] = new $this->modelClass($each);
        }

        return $result;
    }
}