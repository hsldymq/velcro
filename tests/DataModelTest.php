<?php

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Tests\Models\BasicDataModel;
use PHPUnit\Framework\TestCase;
use \RuntimeException;

class DataModelTest extends TestCase
{
    public function testDataModelFunction()
    {
        $data = new BasicDataModel([
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

        $data = new BasicDataModel([
            'ro' => 'hello',
            'cc' => true,
        ]);
        $this->assertFalse(isset($data->a));
        $this->assertSame('hello', $data->ro);
        $this->assertTrue($data->c);

        $data = new BasicDataModel([
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

        $model = new BasicDataModel(['ro' => 'yes']);
        try {
            $model->ro = 'no';
        } catch (ReadonlyException $e) {
            $this->assertEquals(BasicDataModel::class, $e->getClassName());
            $this->assertEquals('ro', $e->getPropertyName());
            throw $e;
        }
    }

    public function testSetPrivateProp_ExpectError()
    {
        $this->expectException(RuntimeException::class);

        $model = new BasicDataModel(['ee' => []]);
        $model->e = ['eee' => 'eeee'];
    }
}