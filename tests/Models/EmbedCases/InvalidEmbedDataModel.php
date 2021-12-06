<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models\EmbedCases;

use Archman\Velcro\Converters\ModelConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class InvalidEmbedDataModel extends DataModel
{
    #[Field('baz1'), ModelConverter]
    public Baz $baz1;
}

class Baz
{

}