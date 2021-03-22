<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Tests\Models\DateTimeModel;
use Archman\DataModel\Tests\Models\BasicModel;
use PHPUnit\Framework\TestCase;

class DataModelTest extends TestCase
{
    public function testAssignProps()
    {
        $data = new BasicModel([
            'aa' => 1,
            'bb' => 'hi',
            'dd' => 1.5,
            'ee' => ['a' => 'a'],
        ]);
        $this->assertSame(1, $data->a);
        $this->assertSame('hi', $data->b);
        $this->assertFalse(isset($data->c));
        $this->assertSame(1.5, $data->getD());
        $this->assertEquals(['a' => 'a'], $data->getE());

        $data = new BasicModel([
            'bb' => 'hello',
            'cc' => true,
        ]);
        $this->assertFalse(isset($data->a));
        $this->assertSame('hello', $data->b);
        $this->assertTrue($data->c);

        $data = new BasicModel([
            'aa' => 3,
            'bb' => 'greeting',
            'cc' => false,
            'dd' => 2.5,
            'ee' => ['b' => 'b'],
        ]);
        $this->assertSame(3, $data->a);
        $this->assertSame('greeting', $data->b);
        $this->assertFalse($data->c);
        $this->assertSame(2.5, $data->getD());
        $this->assertEquals(['b' => 'b'], $data->getE());
    }

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
}