# DataModel
[![Build Status](https://travis-ci.com/hsldymq/data-model.svg?branch=main)](https://travis-ci.com/hsldymq/data-model)
[![codecov](https://codecov.io/gh/hsldymq/data-model/branch/main/graph/badge.svg?token=H9S1V7NSIB)](https://codecov.io/gh/hsldymq/data-model)

```php
<?php

use Archman\DataModel\Converters\DateTimeConverter;
use Archman\DataModel\DataModel;
use Archman\DataModel\Field;
use Archman\DataModel\Readonly;
 
class Foo extends DataModel
{
    #[Field('field1')]
    public int $val1;
    
    #[Field('field2'), DateTimeConverter(DateTimeConverter::ISO_8601)]
    public DateTime $val2;
    
    #[Field('field3'), Readonly]
    public string $roVal3;
}

$foo = new Foo([
    'field1' => 123,
    'field2' => '2021-01-01T00:00:00',
    'field3' => 'readonly value',
]);

assert($foo->val1 === 123);
assert($foo->val2->format('Y-m-d H:i:s') === '2021-01-01 00:00:00');
assert($foo->roVal3 === 'readonly value');
$foo->roVal3 = 'new value'; // it will throw an exception
```

# Requirement
PHP >= 8.0

# Install
```bash
composer require hsldymq/data-model
```