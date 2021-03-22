<?php

declare(strict_types=1);

namespace Archman\DataModel\Attributes;

use Attribute;

#[Attribute]
class Field
{
    public function __construct(private string $fieldName)
    {
    }
}