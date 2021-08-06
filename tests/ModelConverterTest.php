<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests;

use Archman\Velcro\Converters\ModelListConverter;
use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Tests\Models\EmbedDataListModel;
use Archman\Velcro\Tests\Models\Outer;
use PHPUnit\Framework\TestCase;
use TypeError;

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
                ],
            ],
        ]);

        $this->assertCount(2, $model->outerList);

        $this->assertCount(2, $model->outerList[0]->innerList);
        $this->assertEquals(1, $model->outerList[0]->innerList[0]->val);
        $this->assertEquals(2, $model->outerList[0]->innerList[1]->val);

        $this->assertCount(2, $model->outerList[1]->innerList);
        $this->assertEquals(3, $model->outerList[1]->innerList[0]->val);
        $this->assertEquals(4, $model->outerList[1]->innerList[1]->val);


        $model = new EmbedDataListModel([
            'outers' => [
                'outer1' => [
                    'inners' => [
                        'inner1_a' => ['field' => 1],
                        'inner1_b' => ['field' => 2],
                    ],
                ],
                'outer2' => [
                    'inners' => [
                        'inner2_a' => ['field' => 3],
                        'inner2_b' => ['field' => 4],
                    ],
                ],
            ],
        ]);
        $this->assertCount(2, $model->outerList);

        $this->assertCount(2, $model->outerList['outer1']->innerList);
        $this->assertEquals(1, $model->outerList['outer1']->innerList['inner1_a']->val);
        $this->assertEquals(2, $model->outerList['outer1']->innerList['inner1_b']->val);

        $this->assertCount(2, $model->outerList['outer2']->innerList);
        $this->assertEquals(3, $model->outerList['outer2']->innerList['inner2_a']->val);
        $this->assertEquals(4, $model->outerList['outer2']->innerList['inner2_b']->val);

    }

    public function testInvalidFieldType_ExpectError()
    {
        $this->expectException(ConversionException::class);

        new EmbedDataListModel(['outers' => 123]);
    }

    public function testInvalidElementType_ExpectError()
    {
        try {
            new EmbedDataListModel([
                'outers' => [
                    [
                        'inners' => ['test']
                    ]
                ]
            ]);
        } catch (ConversionException $e) {
            $this->assertEquals(Outer::class, $e->getClassName());
            $this->assertEquals('innerList', $e->getPropertyName());
            $this->assertEquals(ModelListConverter::class, $e->getConverterClassName());
            $this->assertEquals(TypeError::class, $e->getRootCause()::class);
        }
    }
}