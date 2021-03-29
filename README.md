# Velcro
[![Build Status](https://travis-ci.com/hsldymq/velcro.svg?branch=main)](https://travis-ci.com/hsldymq/velcro)
[![codecov](https://codecov.io/gh/hsldymq/velcro/branch/main/graph/badge.svg?token=73StDTfHBx)](https://codecov.io/gh/hsldymq/velcro)

更便捷的将数组中数据附给对象属性

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

# 要求
PHP >= 8.0

# 安装

```bash
composer require hsldymq/velcro
```
