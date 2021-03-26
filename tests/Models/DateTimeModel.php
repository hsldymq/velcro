<?php

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\DateTimeConverter;
use Archman\Velcro\Model;
use Archman\Velcro\Field;

class DateTimeModel extends Model
{
    #[Field('timestamp'), DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime1;

    #[Field('timestampDecimal'), DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime2;

    #[Field('timestampMS'), DateTimeConverter(DateTimeConverter::TIMESTAMP_MS)]
    public \DateTime $datetime3;

    #[Field('datetimeStr'), DateTimeConverter(DateTimeConverter::ISO_8601)]
    public \DateTime $datetime4;
}
