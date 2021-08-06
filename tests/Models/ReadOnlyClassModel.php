<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\Readonly;

#[Readonly]
class ReadOnlyClassModel extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2')]
    public int $val2;
}
