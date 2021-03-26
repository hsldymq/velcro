<?php

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\PrimitiveDataModel;
use PHPUnit\Framework\TestCase;

class PrimitiveConverterTest extends TestCase
{
    public function testPrimitiveConverters()
    {
        $model = new PrimitiveDataModel([
            'intField' => true,
            'floatField' => '1.5',
            'boolField' => 100,
            'stringField' => 123,
        ]);
        $this->assertSame(1, $model->intVal);
        $this->assertSame(1.5, $model->floatVal);
        $this->assertTrue($model->boolVal);
        $this->assertSame('123', $model->stringVal);

        $model = new PrimitiveDataModel([
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
        $this->expectException(ConversionException::class);

        new PrimitiveDataModel(['intField' => null]);
    }

    public function testFloatConverter_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new PrimitiveDataModel(['floatField' => function() {}]);
    }

    public function testBoolConverter_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new PrimitiveDataModel(['boolField' => new class(){}]);
    }

    public function testStringConverter_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new PrimitiveDataModel(['stringField' => []]);
    }
}