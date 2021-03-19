<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

interface ConverterInterface
{
    public function convert(mixed $data): mixed;
}