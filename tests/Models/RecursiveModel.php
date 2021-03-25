<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\Converters\DataModelConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

class RecursiveModel extends DataModel
{
    #[Field('floatField')]
    public float $floatValue;

    #[Field('foo1'), DataModelConverter]
    public int|Foo $foo1;

    /** @var Foo */
    #[Field('foo2'), DataModelConverter(Foo::class)]
    public $foo2;

    #[Field('foo3'), DataModelConverter(Foo::class)]
    public Foo|Bar $foo3;

    #[Field('baz1'), DataModelConverter]
    public Baz $baz1;

    #[Field('baz2'), DataModelConverter(Baz::class)]
    public $baz2;
}

class Foo extends DataModel
{
    #[Field('intField')]
    public int $intValue;

    #[Field('bar'), DataModelConverter]
    public Bar $bar;
}

class Bar extends DataModel
{
    #[Field('stringField')]
    public string $stringValue;
}

class Baz
{

}