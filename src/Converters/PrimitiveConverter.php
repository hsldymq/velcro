<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Attribute;

// TODO 待完善
#[Attribute]
class PrimitiveConverter implements ConverterInterface
{
    public function __construct()
    {

    }

    public function convert(mixed $data): mixed
    {
        return $data;
    }
}