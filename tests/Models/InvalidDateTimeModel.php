<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Converters\DateTimeConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

class InvalidDateTimeModel extends DataModel
{
    #[Field('dt')]
    #[DateTimeConverter(-1)]
    public \DateTime $dt;
}
