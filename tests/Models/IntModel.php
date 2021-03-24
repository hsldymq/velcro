<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Converters\IntConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

class IntModel extends DataModel
{
    #[Field('value1')]
    #[IntConverter(['bool', 'float', 'string'])]
    public int $value1;

    #[Field('value2')]
    #[IntConverter(['bool', 'float', 'string'])]
    public int $value2;

    #[Field('value3')]
    #[IntConverter(['bool', 'float', 'string'])]
    public int $value3;
}
