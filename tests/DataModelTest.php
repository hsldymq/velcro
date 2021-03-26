<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Exceptions\ReadonlyException;
use Archman\DataModel\Tests\Models\BasicModel;
use PHPUnit\Framework\TestCase;
use \RuntimeException;

class DataModelTest extends TestCase
{
    public function testDataModelFunction()
    {
        $data = new BasicModel([
            'aa' => 1,
            'ro' => 'hi',
            'dd' => 1.5,
            'ee' => ['a' => 'a'],
        ]);
        $this->assertSame(1, $data->a);
        $this->assertSame('hi', $data->ro);
        $this->assertFalse(isset($data->c));
        $this->assertSame(1.5, $data->getD());
        $this->assertEquals(['a' => 'a'], $data->getE());

        $data = new BasicModel([
            'ro' => 'hello',
            'cc' => true,
        ]);
        $this->assertFalse(isset($data->a));
        $this->assertSame('hello', $data->ro);
        $this->assertTrue($data->c);

        $data = new BasicModel([
            'aa' => 3,
            'ro' => 'greeting',
            'cc' => false,
            'dd' => 2.5,
            'ee' => ['b' => 'b'],
            'ff' => null,
            'gg' => null,
        ]);
        $this->assertSame(3, $data->a);
        $this->assertSame('greeting', $data->ro);
        $this->assertFalse($data->c);
        $this->assertSame(2.5, $data->getD());
        $this->assertEquals(['b' => 'b'], $data->getE());
    }

    public function testSetReadonlyProp_ExpectError()
    {
        $this->expectException(ReadonlyException::class);

        $model = new BasicModel(['ro' => 'yes']);
        try {
            $model->ro = 'no';
        } catch (ReadonlyException $e) {
            $this->assertEquals(BasicModel::class, $e->getClassName());
            $this->assertEquals('ro', $e->getPropertyName());
            throw $e;
        }
    }

    public function testSetPrivateProp_ExpectError()
    {
        $this->expectException(RuntimeException::class);

        $model = new BasicModel(['ee' => []]);
        $model->e = ['eee' => 'eeee'];
    }
}