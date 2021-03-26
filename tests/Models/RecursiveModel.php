<?php

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\ModelConverter;
use Archman\Velcro\Model;
use Archman\Velcro\Field;

class RecursiveModel extends Model
{
    #[Field('floatField')]
    public float $floatValue;

    #[Field('foo1'), ModelConverter]
    public int|Foo $foo1;

    /** @var Foo */
    #[Field('foo2'), ModelConverter(Foo::class)]
    public $foo2;

    #[Field('foo3'), ModelConverter(Foo::class)]
    public false|Foo|Bar $foo3;

    #[Field('baz1'), ModelConverter]
    public Baz $baz1;

    #[Field('baz2'), ModelConverter(Baz::class)]
    public $baz2;
}

class Foo extends Model
{
    #[Field('intField')]
    public int $intValue;

    #[Field('bar'), ModelConverter]
    public Bar $bar;
}

class Bar extends Model
{
    #[Field('stringField')]
    public string $stringValue;
}

class Baz
{

}