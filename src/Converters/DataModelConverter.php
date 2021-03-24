<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\DataModel;
use Archman\DataModel\PropertyType;
use Attribute;

#[Attribute]
class DataModelConverter implements ConverterInterface
{
    public function convert(mixed $fieldValue, PropertyType $type): DataModel
    {
        // TODO
        return $fieldValue;
    }
}