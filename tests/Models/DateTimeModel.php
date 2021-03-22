<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Converters\DateTimeConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

class DateTimeModel extends DataModel
{
    #[Field('timestamp')]
    #[DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime1;

    #[Field('timestampDecimal')]
    #[DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime2;

    #[Field('timestampMS')]
    #[DateTimeConverter(DateTimeConverter::TIMESTAMP_MS)]
    public \DateTime $datetime3;

    #[Field('datetimeStr')]
    #[DateTimeConverter(DateTimeConverter::ISO_8601)]
    public \DateTime $datetime4;
}
