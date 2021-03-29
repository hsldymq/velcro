# Velcro
[![Build Status](https://travis-ci.com/hsldymq/velcro.svg?branch=main)](https://travis-ci.com/hsldymq/velcro)
[![codecov](https://codecov.io/gh/hsldymq/velcro/branch/main/graph/badge.svg?token=73StDTfHBx)](https://codecov.io/gh/hsldymq/velcro)

```php
<?php

use Archman\Velcro\Converters\DateTimeConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\Readonly;
 
class Foo extends DataModel
{
    #[Field('field1')]
    public int $val1;

    #[Field('field2'), DateTimeConverter(DateTimeConverter::ISO_8601)]
    public DateTime $val2;

    #[Field('field3'), Readonly]
    public string $val3;
}

$foo = new Foo([
    'field1' => 123,
    'field2' => '2021-01-01T00:00:00',
    'field3' => 'readonly value',
]);

assert($foo->val1 === 123);
assert($foo->val2->format('Y-m-d H:i:s') === '2021-01-01 00:00:00');
assert($foo->val3 === 'readonly value');
$foo->val3 = 'new value'; // it will throw an exception
```

# 简介
PHP现在有了type hinting, 有typed properties,有了union types，再配合strict types，我们写出的代码相对过去能拥有更佳的类型安全.

所以过去一些使用关联数组来组织信息的地方，我现在可能会使用值对象的方式来组织信息.

以前一部分类似于这样`doSomething($foo['bar'])`的代码

现在会写成这样`doSomething($foo->bar)`

那么：
1. 解释器就可能更早的抛出类型相关的错误
2. 我能更从容的重构代码
3. 使用静态分析器时或许能发现更多潜在问题

所以当我真正要做这件事的时候，我就需要把关联数组中的数据一个个地赋给对象的属性，极其枯燥且易错.

“这么麻烦，那我还是用数组吧！”

“或者写一个小玩意，它帮我把这件事做了。”

于是我写了这个。

# 要求
PHP >= 8.0

# 安装

```bash
composer require hsldymq/velcro
```
