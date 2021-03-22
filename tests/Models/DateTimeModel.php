<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Attributes\Converter;
use Archman\DataModel\Converters\DateTimeConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Attributes\Field;

class DateTimeModel extends DataModel
{
    #[Field('timestamp')]
    #[Converter(DateTimeConverter::class), DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime1;

    #[Field('timestampDecimal')]
    #[Converter(DateTimeConverter::class), DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public \DateTime $datetime2;

    #[Field('timestampMS')]
    #[Converter(DateTimeConverter::class), DateTimeConverter(DateTimeConverter::TIMESTAMP_MS)]
    public \DateTime $datetime3;

    #[Field('datetimeStr')]
    #[Converter(DateTimeConverter::class), DateTimeConverter(DateTimeConverter::ISO_8601)]
    public \DateTime $datetime4;
}
