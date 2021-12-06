<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\BackedEnumConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

class SimpleEnumModel extends DataModel
{
    #[Field('enumValue1'), BackedEnumConverter]
    public SimpleBackedEnum $e1;

    #[Field('enumValue2'), BackedEnumConverter]
    public readonly SimpleBackedEnum $e2;

    #[Field('enumValue3'), BackedEnumConverter(SimpleBackedEnum::Unknown)]
    public SimpleBackedEnum $e3;
}