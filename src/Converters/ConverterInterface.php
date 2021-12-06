<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\Property;

interface ConverterInterface
{
    public function bindToProperty(Property $property);

    public function convert(mixed $fieldValue): mixed;
}