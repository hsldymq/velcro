<?php

namespace Archman\DataModel\Tests;

use Archman\DataModel\Property;
use Archman\DataModel\Tests\Models\BasicModel;
use Archman\DataModel\Tests\Models\RecursiveModel;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function testIsNullable()
    {
        $model = new BasicModel([
            'aa' => 3,
            'bb' => 'greeting',
            'cc' => false,
            'dd' => 2.5,
            'ee' => ['b' => 'b'],
            'ff' => null,
            'gg' => null,
        ]);
        $refl = new \ReflectionObject($model);

        $propName = 'a';
        $p = new Property($refl->getProperty($propName));
        $this->assertFalse($p->isNullable());

        $propName = 'f';
        $p = new Property($refl->getProperty($propName));
        $this->assertTrue($p->isNullable());

        $propName = 'g';
        $p = new Property($refl->getProperty($propName));
        $this->assertTrue($p->isNullable());
    }

    public function testIsMixed()
    {
        $model = new BasicModel([
            'aa' => 3,
            'bb' => 'greeting',
            'cc' => false,
            'dd' => 2.5,
            'ee' => ['b' => 'b'],
            'ff' => null,
            'gg' => null,
        ]);
        $refl = new \ReflectionObject($model);

        $propName = 'a';
        $p = new Property($refl->getProperty($propName));
        $this->assertFalse($p->isMixed());

        $propName = 'f';
        $p = new Property($refl->getProperty($propName));
        $this->assertFalse($p->isMixed());

        $propName = 'g';
        $p = new Property($refl->getProperty($propName));
        $this->assertTrue($p->isMixed());
    }

    public function testIsDataModel()
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
        $refl = new \ReflectionObject($model);

        $propName = 'floatValue';
        $p = new Property($refl->getProperty($propName));
        $this->assertFalse($p->isDataModel());

        $propName = 'foo1';
        $p = new Property($refl->getProperty($propName));
        $this->assertTrue($p->isDataModel());

        $propName = 'foo2';
        $p = new Property($refl->getProperty($propName));
        $this->assertFalse($p->isDataModel());

        $propName = 'foo3';
        $p = new Property($refl->getProperty($propName));
        $this->assertTrue($p->isDataModel());
    }
}