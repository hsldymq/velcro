<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Tests\Models\DateTimeModel;
use Archman\DataModel\Tests\Models\InvalidDateTimeModel;
use PHPUnit\Framework\TestCase;

class DateTimeConverterTest extends TestCase
{
    public function testDateTimeConverter()
    {
        $data = new DateTimeModel([
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
        $this->expectException(\Exception::class);

        new DateTimeModel(['datetimeStr' => 'dsjfkalsdjflksajfkl0']);
    }

    public function testInvalidType_ExpectError()
    {
        $this->expectException(\InvalidArgumentException::class);

        new InvalidDateTimeModel(['dt' => 123]);
    }
}