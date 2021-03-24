<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Tests\Models\IntModel;
use PHPUnit\Framework\TestCase;
use TypeError;

class IntConverterTest extends TestCase
{
    public function testIntConverter()
    {
        $model = new IntModel([
            'value1' => false,
            'value2' => 1.2,
            'value3' => "123",
        ]);

        $this->assertEquals(0, $model->value1);
        $this->assertEquals(1, $model->value2);
        $this->assertEquals(123, $model->value3);
    }

    public function testIntConverter_ExpectError()
    {
        $this->expectException(TypeError::class);

        new IntModel(['value3' => null]);
    }
}