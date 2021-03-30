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

所以过去一些使用关联数组来组织信息的地方，现在我可能会使用值对象的方式来组织信息.

以前一部分类似于这样`doSomething($foo['bar'])`的代码

现在会写成这样`doSomething($foo->bar)`

那么：
1. 解释器就可能更早的抛出类型相关的错误
2. 我能更从容的重构代码
3. 使用静态分析器时或许能发现更多潜在问题

所以当我真正要做这件事的时候，我就需要把关联数组中的数据一个个地赋给对象的属性，极其枯燥且易错.

这种事情应该交给程序来做,于是有了这个小玩意。

# 要求
PHP >= 8.0

# 安装

```bash
composer require hsldymq/velcro
```

# 使用

## 基础使用
这里展示一个最基本的用法.
```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class Foo extends DataModel
{
    #[Field('field')]
    public int $bar;
}

$foo = new Foo(['field' => 1]);

doSomething($foo->bar);
```

你至少需要用到两个类, `\Archman\Velcro\DataModel` 和 `\Archman\Velcro\Field`

前者作为基类被你的数据模型类继承, 后者利用PHP 8 Attribute特性,用来关联你的类属性与其对应数组键名. 

简短的几句声明之后,你就可以把数组作为构造参数传给Foo构造函数了,Foo内部在帮你做了类似这样的事情:

```php
<?php

class Foo
{
    public int $bar; 

    public function __construct(array $data)
    {
        if (array_key_exists('field', $data)) {
            $this->bar = $data['field'];
        }
    }
}
```

### 类型匹配
尽管内部帮你解决了赋值的问题, 但是它不会帮你匹配类型,更不会自动帮你转换类型, 所以当你的类属性和数据字段的类型不一致时,会抛出异常,因为Velcro的类都是以strict_types模式定义的.

## 数据转换器
然而你定义的类属性,可能是任意类型. 同时你的数据可能不是来自于程序内部,有可能是http请求/响应体,或者外部配置/存储,异步消息等,双方的类型可能并不匹配.

此时,你需要用到数据转换器, 把来源数据转换成其对应属性的类型, 这样你可以用你更舒服的方式去定义数据类.

### 使用数据转换器
当我们从第三方接口获得数据中包含了一个时间戳的字段,比如`$data['time']`,而在我们的代码中需要以`DateTime`类型来使用, 与其我们手动的编写转换代码, 我们可以这样定义
```php
<?php

use Archman\Velcro\Converters\DateTimeConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class Response extends DataModel
{
    #[Field('time')]
    #[DateTimeConverter(DateTimeConverter::TIMESTAMP)]
    public DateTime $datetime;
}
new Response(['time' => 1609430400]);
```

Velcro会先使用`DateTimeConverter`帮你把时间戳转换成DateTime类型再赋给对应的属性.

Velcro中预先定义了少量的转换器,用来应对不同的场景.

### 嵌套DataModel
你可以使用`ModelConverter`和`ModelListConverter`这两个转换器,实现数据模型的嵌套嵌套.

```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\Converters\ModelConverter;
use Archman\Velcro\Converters\ModelListConverter;

// 假设你有这样一套数据:
$data = [
    "students" => [
        [
          "name" => "Alice",
          "age" => 8,
        ],
        [
          "name" => "Bob",
          "age" => 10,
        ]
    ],
    "school" => [
        "name" => "xxx",
        "address" => "yyy",
    ],
];

// 你对users和school各自建立数据模型

class Info extends DataModel
{
    /** @var Student[] */
    #[Field('students')]
    #[ModelListConverter(Student::class)]
    public array $studentList;
    
    #[Field('school')]
    #[ModelConverter]
    public School $school;
}

class Student extends DataModel
{
    #[Field('name')]
    public string $name;
    
    #[Field('age')]
    public int $age;
}

class School extends DataModel
{
    #[Field('name')]
    public string $name;
    
    #[Field('address')]
    public string $address;
}

$info = new Info($data);

assert(count($info->studentList) === 2);
assert($info->studentList[0]->name === 'Alice');
assert($info->school->name === 'xxx');
```

**通过实现ConverterInterface接口,你可以实现自己的数据转换器**

## 只读属性
有些情况下,你可能需要你的模型是不可变的, 例如你有一个全局配置的模型对象,你不希望使用方更改配置的值,你可以对类或属性标记Readonly来达到目的
```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Field;
use Archman\Velcro\Readonly;

// 将属性标记Readonly, 使得指定属性变为只读
class ConfigA extends DataModel
{
    #[Field('conf1')]
    #[Readonly]
    public string $config1;
    
    #[Field('conf2')]
    public int $config2;
}

$c = new ConfigA([
    'conf1' => 'xxx',
    'conf2' => 111,
]);
try {
    $c->config1 = 'yyy';
} catch(Throwable $e) {
    assert($e::class === ReadonlyException::class);
}
$c->config2 = 222;
assert($c->config2 === 222);


// 将类标记为Readonly, 其中所有标记了Field的属性都会变为只读
#[Readonly]
class ConfigB extends DataModel
{
    #[Field('conf1')]
    public string $config1;
    
    #[Field('conf2')]
    public int $config2;
    
    public string $config3;
}

$c = new ConfigB([
    'conf1' => 'xxx',
    'conf2' => 111,
]);
try {
    $c->config1 = 'yyy';
} catch(Throwable $e) {
    assert($e::class === ReadonlyException::class);
}

try {
    $c->config2 = 222;
} catch(Throwable $e) {
    assert($e::class === ReadonlyException::class);
}

$c->config3 = 'xxx'; // 没有标记Field, 不会抛出异常
```
