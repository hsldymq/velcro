<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\DateTimeCases\DateTimeDataModel;
use Archman\Velcro\Tests\Models\DateTimeCases\InvalidDateTimeDataModel;
use PHPUnit\Framework\TestCase;

class DateTimeConverterTest extends TestCase
{
    public function testDateTimeConverter()
    {
        $data = new DateTimeDataModel([
            'timestamp' => 1609459200,
            'timestampDecimal' => 1609459200.123,
            'timestampMS' => 1609459200123,
            'datetimeStr' => '2021-01-01T00:00:00.123+0000',
        ]);
        $this->assertEquals('2021-01-01 00:00:00', $data->datetime1->setTimezone(new \DateTimeZone('+0000'))->format('Y-m-d H:i:s'));
        $this->assertEquals('2021-01-01 00:00:00.123000', $data->datetime2->setTimezone(new \DateTimeZone('+0000'))->format('Y-m-d H:i:s.u'));
        $this->assertEquals('2021-01-01 00:00:00.123000', $data->datetime3->setTimezone(new \DateTimeZone('+0000'))->format('Y-m-d H:i:s.u'));
        $this->assertEquals('2021-01-01 00:00:00.123000', $data->datetime4->setTimezone(new \DateTimeZone('+0000'))->format('Y-m-d H:i:s.u'));
    }

    public function testInvalidTimeValue_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new DateTimeDataModel(['datetimeStr' => 'dsjfkalsdjflksajfkl0']);
    }

    public function testInvalidType_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new InvalidDateTimeDataModel(['dt' => 123]);
    }
}