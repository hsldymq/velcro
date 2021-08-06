<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\Readonly;

class ReadOnlyFieldModel extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2'), Readonly]
    public int $val2;
}
