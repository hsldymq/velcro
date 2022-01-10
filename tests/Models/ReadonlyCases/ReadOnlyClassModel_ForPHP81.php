<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\ReadonlyCases;

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

#[RO]
class ReadOnlyClassModel_ForPHP81 extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2')]
    public int $val2;

    #[Field('field3')]
    public readonly int $val3;
}
