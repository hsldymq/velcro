<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;

interface ConverterInterface
{
    public function convert(mixed $data, PropertyType $type): mixed;
}