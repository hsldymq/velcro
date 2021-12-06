<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\ReadonlyCases;

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

class ReadOnlyFieldModel extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2'), RO]
    public int $val2;
}
