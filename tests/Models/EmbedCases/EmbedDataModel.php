<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\EmbedCases;

use Archman\Velcro\Converters\ModelConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class EmbedDataModel extends DataModel
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
}

class Foo extends DataModel
{
    #[Field('intField')]
    public int $intValue;

    #[Field('bar'), ModelConverter]
    public Bar $bar;
}

class Bar extends DataModel
{
    #[Field('stringField')]
    public string $stringValue;
}