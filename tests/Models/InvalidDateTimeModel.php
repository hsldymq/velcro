<?php

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\DateTimeConverter;
use Archman\Velcro\Model;
use Archman\Velcro\Field;

class InvalidDateTimeModel extends Model
{
    #[Field('dt'), DateTimeConverter(-1)]
    public \DateTime $dt;
}
