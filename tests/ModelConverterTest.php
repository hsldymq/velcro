<?php

namespace Archman\Velcro\Tests;

use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\EmbedDataListModel;
use PHPUnit\Framework\TestCase;

class ModelConverterTest extends TestCase
{
    public function testRecursiveDataModel()
    {
        $model = new EmbedDataListModel([
            'outers' => [
                [
                    'inners' => [
                        ['field' => 1],
                        ['field' => 2],
                    ],
                ],
                [
                    'inners' => [
                        ['field' => 3],
                        ['field' => 4],
                    ],
                ]
            ],
        ]);

        $this->assertCount(2, $model->outerList);

        $this->assertCount(2, $model->outerList[0]->innerList);
        $this->assertEquals(1, $model->outerList[0]->innerList[0]->val);
        $this->assertEquals(2, $model->outerList[0]->innerList[1]->val);

        $this->assertCount(2, $model->outerList[1]->innerList);
        $this->assertEquals(3, $model->outerList[1]->innerList[0]->val);
        $this->assertEquals(4, $model->outerList[1]->innerList[1]->val);

    }

    public function testInvalidFieldType_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new EmbedDataListModel(['outers' => 123]);
    }

    public function testInvalidElementType_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new EmbedDataListModel(['outers' => [['inners' => ['test']]]]);
    }
}