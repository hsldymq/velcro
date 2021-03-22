<?php

declare(strict_types=1);

namespace Archman\DataModel;

use Attribute;

#[Attribute]
class Field
{
    public function __construct(private string $fieldName)
    {
    }
}