<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;

// TODO 待完善
#[Attribute]
class PrimitiveConverter implements ConverterInterface
{
    public function convert(mixed $data, PropertyType $type): mixed
    {
        return $data;
    }
}