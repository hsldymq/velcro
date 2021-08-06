<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Converters\ModelListConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class EmbedDataListModel extends DataModel
{
    /** @var Outer[] */
    #[Field('outers'), ModelListConverter(Outer::class)]
    public array $outerList;
}

class Outer extends DataModel
{
    /** @var Inner[] */
    #[Field('inners'), ModelListConverter(Inner::class)]
    public array $innerList;
}

class Inner extends DataModel
{
    #[Field('field')]
    public int $val;
}