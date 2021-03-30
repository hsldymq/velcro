<?php

namespace Archman\Velcro\Tests;

use Archman\Velcro\Property;
use Archman\Velcro\Tests\Models\BasicDataModel;
use Archman\Velcro\Tests\Models\RecursiveDataModel;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function testIsNullable()
    {
        $model = new BasicDataModel([
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
        $p = new Property($refl->getProperty($propName), 'aa');
        $this->assertFalse($p->isNullable());

        $propName = 'f';
        $p = new Property($refl->getProperty($propName), 'ff');
        $this->assertTrue($p->isNullable());

        $propName = 'g';
        $p = new Property($refl->getProperty($propName), 'gg');
        $this->assertTrue($p->isNullable());
    }

    public function testIsMixed()
    {
        $model = new BasicDataModel([
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
        $p = new Property($refl->getProperty($propName), 'aa');
        $this->assertFalse($p->isMixed());

        $propName = 'f';
        $p = new Property($refl->getProperty($propName), 'ff');
        $this->assertFalse($p->isMixed());

        $propName = 'g';
        $p = new Property($refl->getProperty($propName), 'gg');
        $this->assertTrue($p->isMixed());
    }

    public function testIsDataModel()
    {
        $model = new RecursiveDataModel([
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
        $p = new Property($refl->getProperty($propName), 'floatField');
        $this->assertFalse($p->isDataModel());

        $propName = 'foo1';
        $p = new Property($refl->getProperty($propName), 'foo1');
        $this->assertTrue($p->isDataModel());

        $propName = 'foo2';
        $p = new Property($refl->getProperty($propName), 'foo2');
        $this->assertFalse($p->isDataModel());

        $propName = 'foo3';
        $p = new Property($refl->getProperty($propName), 'foo3');
        $this->assertTrue($p->isDataModel());
    }
}