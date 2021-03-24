<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Tests\Models\PrimitiveModel;
use PHPUnit\Framework\TestCase;
use TypeError;

class PrimitiveConverterTest extends TestCase
{
    public function testPrimitiveConverters()
    {
        $model = new PrimitiveModel([
            'intField' => true,
            'floatField' => '1.5',
            'boolField' => 100,
            'stringField' => 123,
        ]);
        $this->assertSame(1, $model->intVal);
        $this->assertSame(1.5, $model->floatVal);
        $this->assertTrue($model->boolVal);
        $this->assertSame('123', $model->stringVal);

        $model = new PrimitiveModel([
            'intField' => 1,
            'floatField' => 1.5,
            'boolField' => true,
            'stringField' => '123',
        ]);
        $this->assertSame(1, $model->intVal);
        $this->assertSame(1.5, $model->floatVal);
        $this->assertTrue($model->boolVal);
        $this->assertSame('123', $model->stringVal);
    }

    public function testIntConverter_ExpectError()
    {
        $this->expectException(TypeError::class);

        new PrimitiveModel(['intField' => null]);
    }

    public function testFloatConverter_ExpectError()
    {
        $this->expectException(TypeError::class);

        new PrimitiveModel(['floatField' => function() {}]);
    }

    public function testBoolConverter_ExpectError()
    {
        $this->expectException(TypeError::class);

        new PrimitiveModel(['boolField' => new class(){}]);
    }

    public function testStringConverter_ExpectError()
    {
        $this->expectException(TypeError::class);

        new PrimitiveModel(['stringField' => []]);
    }
}