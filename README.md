# 对象属性自动赋值器
[![Build Status](https://travis-ci.com/hsldymq/data-model.svg?branch=main)](https://travis-ci.com/hsldymq/data-model)
[![codecov](https://codecov.io/gh/hsldymq/data-model/branch/main/graph/badge.svg?token=H9S1V7NSIB)](https://codecov.io/gh/hsldymq/data-model)

```php
<?php

use Archman\DataModel\DataModel;
use Archman\DataModel\Field;
 
class Foo extends DataModel
{
    #[Field('f')]
    public int $val;
}

$foo = new Foo(['f' => 123]);

assert($foo->val === 123);

```

# Requirement
PHP >= 8.0

# Install
```bash
composer require hsldymq/data-model
```

# 背景
在PHP中,当我们想要使用或传递一些比较复杂的信息时,比如配置,事件信息,从外部获取的JSON数据等,常见的做法是将他们转换成关联数组使用.

当我们使用这类数组时,它有很多弊端:
1. key的名称写错,导致读取了不存在的值 - 运行时错误
2. 将数组中的某个元素作为参数传递给某函数,但是其类型不一致 - 运行时错误
3. 重构这类数组时会比较困难,尤其是当许多代码都在使用它时
4. 静态分析工具很难发现问题
5. 无法充分利用开发工具的不由

PHP 8的类型系统相对于之前的有了进一步的完善