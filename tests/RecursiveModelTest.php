<?php

namespace Archman\Velcro\Tests;

use Archman\Velcro\Converters\DataModelConverter;
use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\Foo;
use Archman\Velcro\Tests\Models\RecursiveModel;
use PHPUnit\Framework\TestCase;

class RecursiveModelTest extends TestCase
{
    public function testRecursiveDataModel()
    {
        $model = new RecursiveModel([
            'floatField' => 1.5,
            'foo1' => [
                'intField' => 111,
                'bar' => [
                    'stringField' => '111',
                ],
            ],
            'foo2' => [
                'intField' => 222,
                'bar' => [
                    'stringField' => '222',
                ],
            ],
            'foo3' => [
                'intField' => 333,
                'bar' => [
                    'stringField' => '333',
                ]
            ],
        ]);

        $this->assertSame(1.5, $model->floatValue);
        $this->assertSame(111, $model->foo1->intValue);
        $this->assertSame('111', $model->foo1->bar->stringValue);
        $this->assertSame(222, $model->foo2->intValue);
        $this->assertSame('222', $model->foo2->bar->stringValue);
        $this->assertSame(Foo::class, $model->foo3::class);
        $this->assertSame(333, $model->foo3->intValue);
        $this->assertSame('333', $model->foo3->bar->stringValue);
    }

    public function testNonDataModelFieldConversion1_ExpectError()
    {
        $this->expectException(ConversionException::class);

        try {
            new RecursiveModel(['baz1' => []]);
        } catch (ConversionException $e) {
            $this->assertEquals(RecursiveModel::class, $e->getClassName());
            $this->assertEquals('baz1', $e->getPropertyName());
            $this->assertEquals(DataModelConverter::class, $e->getConverterClassName());
            throw $e;
        }
    }

    public function testNonDataModelFieldConversion2_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new RecursiveModel(['baz2' => []]);
    }
}