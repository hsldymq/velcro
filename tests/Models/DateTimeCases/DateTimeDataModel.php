<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\DateTimeCases;

use Archman\Velcro\Converters\DateTimeImmutableConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class DateTimeDataModel extends DataModel
{
    #[Field('timestamp'), DateTimeImmutableConverter(DateTimeImmutableConverter::TIMESTAMP)]
    public \DateTimeImmutable $datetime1;

    #[Field('timestampDecimal'), DateTimeImmutableConverter(DateTimeImmutableConverter::TIMESTAMP)]
    public \DateTimeImmutable $datetime2;

    #[Field('timestampMS'), DateTimeImmutableConverter(DateTimeImmutableConverter::TIMESTAMP_MS)]
    public \DateTimeImmutable $datetime3;

    #[Field('datetimeStr'), DateTimeImmutableConverter(DateTimeImmutableConverter::ISO_8601)]
    public \DateTimeImmutable $datetime4;
}
