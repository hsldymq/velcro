<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Tests\Models\ReadOnlyClassModel;
use Archman\Velcro\Tests\Models\ReadOnlyFieldModel;
use PHPUnit\Framework\TestCase;

class ReadonlyTest extends TestCase
{
    public function testReadonlyField()
    {
        $model = new ReadOnlyFieldModel([
            'field1' => 111,
            'field2' => 222,
        ]);

        $model->val1 = 123;
        $this->assertEquals(123, $model->val1);

        $this->expectException(ReadonlyException::class);
        $model->val2 = 456;
    }

    public function testReadonlyClass()
    {
        $model = new ReadOnlyClassModel([
            'field1' => 111,
            'field2' => 222,
        ]);

        try {
            $model->val1 = 123;
        } catch (\Throwable $e) {
            $this->assertEquals(ReadonlyException::class, $e::class);
        }

        try {
            $model->val2 = 456;
        } catch (\Throwable $e) {
            $this->assertEquals(ReadonlyException::class, $e::class);
        }
    }
}