<?php

declare(strict_types=1);

namespace Archman\Velcro;

use Attribute;

#[Attribute]
class Field
{
    public function __construct(private string $fieldName)
    {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}