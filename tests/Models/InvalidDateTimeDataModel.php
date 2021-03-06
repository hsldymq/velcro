<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\DateTimeConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class InvalidDateTimeDataModel extends DataModel
{
    #[Field('dt'), DateTimeConverter(-1)]
    public \DateTime $dt;
}
