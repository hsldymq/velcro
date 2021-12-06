<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Tests\Models\ReadonlyCases\ReadOnlyClassModel;
use Archman\Velcro\Tests\Models\ReadonlyCases\ReadOnlyClassModel_ForPHP81;
use Archman\Velcro\Tests\Models\ReadonlyCases\ReadOnlyFieldModel;
use Archman\Velcro\Tests\Models\ReadonlyCases\ReadOnlyFieldModel_ForPHP81;
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

    /**
     * @requires PHP 8.1.0
     */
    public function testReadonlyField_ForPHP81Readonly()
    {
        $model1 = new ReadOnlyFieldModel_ForPHP81([
            'field1' => 111,
            'field2' => 222,
            'field3' => 333,
        ]);
        $this->assertEquals(111, $model1->val1);
        $this->assertEquals(222, $model1->val2);
        $this->assertEquals(333, $model1->val3);

        $model2 = new ReadOnlyFieldModel_ForPHP81([
            'field1' => 777,
            'field2' => 888,
            'field3' => 999,
        ]);
        $this->assertEquals(777, $model2->val1);
        $this->assertEquals(888, $model2->val2);
        $this->assertEquals(999, $model2->val3);
    }

    /**
     * @requires PHP 8.1.0
     */
    public function testReadonlyField_ForPHP81Readonly_ExceptError()
    {
        $model = new ReadOnlyFieldModel_ForPHP81([
            'field1' => 111,
            'field2' => 222,
        ]);

        $model->val1 = 123;
        $this->assertEquals(123, $model->val1);

        $this->expectException(\Error::class);
        $model->val2 = 456;
    }

    public function testReadonlyClass()
    {
        $model = new ReadOnlyClassModel([
            'field1' => 111,
            'field2' => 222,
        ]);
        $this->assertEquals(111, $model->val1);
        $this->assertEquals(222, $model->val2);

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

    /**
     * @requires PHP 8.1.0
     */
    public function testReadonlyClass_ForPHP81Readonly()
    {
        $model1 = new ReadOnlyClassModel_ForPHP81([
            'field1' => 111,
            'field2' => 222,
            'field3' => 333,
        ]);
        $this->assertEquals(111, $model1->val1);
        $this->assertEquals(222, $model1->val2);
        $this->assertEquals(333, $model1->val3);

        $model2 = new ReadOnlyClassModel_ForPHP81([
            'field1' => 777,
            'field2' => 888,
            'field3' => 999,
        ]);
        $this->assertEquals(777, $model2->val1);
        $this->assertEquals(888, $model2->val2);
        $this->assertEquals(999, $model2->val3);
    }

    /**
     * @requires PHP 8.1.0
     */
    public function testReadonlyClass_ForPHP81Readonly_ExceptError()
    {
        $model = new ReadOnlyClassModel_ForPHP81([
            'field1' => 111,
            'field2' => 222,
            'field3' => 333,
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

        try {
            $model->val3 = 789;
        } catch (\Throwable $e) {
            $this->assertEquals(\Error::class, $e::class);
        }
    }
}