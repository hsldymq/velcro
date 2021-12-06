<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\BackedEnumCases\SimpleBackedEnum;
use Archman\Velcro\Tests\Models\BackedEnumCases\SimpleEnumModel;
use PHPUnit\Framework\TestCase;

class BackedEnumTest extends TestCase
{
    /**
     * @requires PHP 8.1.0
     */
    public function testBackedEnum()
    {
        $model = new SimpleEnumModel([
           'enumValue1' => 1,
           'enumValue2' => 2,
           'enumValue3' => 9999,
        ]);
        $this->assertEquals(SimpleBackedEnum::V1, $model->e1);
        $this->assertEquals(SimpleBackedEnum::V2, $model->e2);
        $this->assertEquals(SimpleBackedEnum::Unknown, $model->e3);
    }

    /**
     * @requires PHP 8.1.0
     */
    public function testBackedEnum_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new SimpleEnumModel([
            'enumValue1' => 9999,
        ]);
    }
}