<?php

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\BoolConverter;
use Archman\Velcro\Converters\FloatConverter;
use Archman\Velcro\Converters\IntConverter;
use Archman\Velcro\Converters\StringConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class PrimitiveModel extends DataModel
{
    #[Field('intField'), IntConverter(['bool', 'float', 'string'])]
    public int $intVal;

    #[Field('floatField'), FloatConverter(['bool', 'int', 'string'])]
    public float $floatVal;

    #[Field('boolField'), BoolConverter(['int', 'float'])]
    public bool $boolVal;

    #[Field('stringField'), StringConverter(['int', 'float'])]
    public string $stringVal;
}
