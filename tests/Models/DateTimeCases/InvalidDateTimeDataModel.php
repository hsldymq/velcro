<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\DateTimeCases;

use Archman\Velcro\Converters\DateTimeImmutableConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class InvalidDateTimeDataModel extends DataModel
{
    #[Field('dt'), DateTimeImmutableConverter(-1)]
    public \DateTime $dt;
}
