<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\Type;

interface ConverterInterface
{
    public function convert(mixed $fieldValue, Type $propertyType): mixed;
}