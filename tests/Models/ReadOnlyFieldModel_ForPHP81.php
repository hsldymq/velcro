<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

class ReadOnlyFieldModel_ForPHP81 extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2')]
    public readonly int $val2;

    #[Field('field3'), RO]      // 属性标注了readonly关键字, RO会失效
    public readonly int $val3;
}
