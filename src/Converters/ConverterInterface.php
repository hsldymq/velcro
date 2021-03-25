<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\Property;

interface ConverterInterface
{
    public function convert(mixed $fieldValue, Property $property): mixed;
}