<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Converters\BoolConverter;
use Archman\DataModel\Converters\FloatConverter;
use Archman\DataModel\Converters\IntConverter;
use Archman\DataModel\Converters\StringConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

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
