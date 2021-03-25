<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Tests\Models\Foo;
use Archman\DataModel\Tests\Models\RecursiveModel;
use PHPUnit\Framework\TestCase;
use TypeError;

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
        $this->expectException(TypeError::class);

        new RecursiveModel(['baz1' => []]);
    }

    public function testNonDataModelFieldConversion2_ExpectError()
    {
        $this->expectException(TypeError::class);

        new RecursiveModel(['baz2' => []]);
    }
}