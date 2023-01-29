# Velcro
![tests](https://github.com/hsldymq/velcro/actions/workflows/unit-tests.yml/badge.svg)
[![codecov](https://codecov.io/gh/hsldymq/velcro/branch/main/graph/badge.svg?token=73StDTfHBx)](https://codecov.io/gh/hsldymq/velcro)

```php
<?php

use Archman\Velcro\Converters\DateTimeImmutableConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;
use Archman\Velcro\RO;
 
class Foo extends DataModel
{
    #[Field('f1')]
    public int $p1;

    #[Field('f2'), DateTimeImmutableConverter(DateTimeImmutableConverter::ISO_8601)]
    public DateTimeImmutable $p2;

    #[Field('f3'), RO]
    public string $p3;
    
    #[Field('f4')]
    public readonly string $p4;
}

$foo = new Foo([
    'f1' => 123,
    'f2' => '2021-01-01T00:00:00',
    'f3' => 'value for readonly field',
    'f4' => 'value for PHP 8.1 readonly field'
]);

assert($foo->p1 === 123);
assert($foo->p2->format('Y-m-d H:i:s') === '2021-01-01 00:00:00');
assert($foo->p3 === 'value for readonly field');
assert($foo->p4 === 'value for PHP 8.1 readonly field');
$foo->p3 = 'new value'; // It throws an exception.
```

# 简介
这个库帮助你用更少的代码自动将关联数组的值附给对象的属性.

### 什么场景下我会需要这种东西?
当你需要从接口请求参数来创建DTO; 当你需要通过对象的形式来管理配置; 抑或者从外部数据源得到的数据恢复成对象时。

从这些预先定义好的数据恢复时，往往就需要大量无趣的组装操作，通常还伴随着一些前置条件逻辑，比如判断字段是否存在，类型是否需要转换。

用代码生成工具是一种减少人力的方法。 而这个库是另一种方法，它让你只需要定义类属性所关联的字段，剩下的组装操作由库来完成。

# 要求
PHP >= 8.0

# 安装

```bash
composer require hsldymq/velcro
```

# 使用

## 基本用法 (Basic)
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

## 数据转换器 (Converter)
然而你定义的类属性,可能是任意类型. 同时你的数据可能不是来自于程序内部,有可能是http请求/响应体,或者外部配置/存储,异步消息等,双方的类型可能并不匹配.

此时,你需要用到数据转换器, 把来源数据转换成其对应属性的类型, 这样你可按照使用上更舒服的方式去定义类的属性.

### 使用数据转换器 (Using Converter)
当我们从第三方接口获得数据中包含了一个时间戳的字段,比如`$data['time']`,而在我们的代码中需要以`DateTimeImmutable`类型来使用, 与其我们手动的编写转换代码, 我们可以这样定义

```php
<?php

use Archman\Velcro\Converters\DateTimeImmutableConverter;
use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

class Response extends DataModel
{
    #[Field('time')]
    #[DateTimeImmutableConverter(DateTimeImmutableConverter::TIMESTAMP)]
    public DateTimeImmutable $datetime;
}
new Response(['time' => 1609430400]);
```

Velcro会先使用`DateTimeConverter`帮你把时间戳转换成 DateTimeImmutable 类型再赋给对应的属性.

Velcro中预先定义了一些的转换器,用来应对不同的场景.

### 嵌套DataModel
你可以使用`ModelConverter`和`ModelListConverter`这两个转换器,实现数据模型的嵌套.

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
        's' => [
          "name" => "Bob",
          "age" => 10,
        ]
    ],
    "school" => [
        "name" => "xxx",
        "address" => "yyy",
    ],
];

// 你对students和school各自建立数据模型

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
assert($info->studentList['s']->name === 'Bob');
assert($info->school->name === 'xxx');
```

**通过实现ConverterInterface接口,你可以实现自己的数据转换器**

## 只读属性 (Readonly Property)
你可能需要你的模型属性是只读的, 但是只读属性在PHP 8.0下并非一个语言特性， 因此你可以对类或属性标记RO来达到目的

```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

// 将属性添加RO注解, 会使该属性变为只读
class ConfigA extends DataModel
{
    #[Field('conf1'), RO]
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


// 当将类添加RO注解, 等同于将其中所有标记了Field都会变为只读
#[RO]
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

因为PHP 8.1开始增加了readonly关键字，所以当你的环境使用8.1以及之后的版本，你可以抛弃RO直接使用readonly关键来标记只读属性来达到相同的目的
```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Exceptions\ReadonlyException;
use Archman\Velcro\Field;
use Archman\Velcro\RO;

// 将属性添加RO注解, 会使该属性变为只读
class ConfigX extends DataModel
{
    #[Field('conf1')]       // 它跟config2有相同的效果
    public readonly string $config1;
    
    #[Field('conf2'), RO]
    public int $config2;
}

```

## 私有属性
在上面的例子中,都是使用public属性进行演示. 但实际上Velcro同样能赋值给protected和private属性

```php
<?php

use Archman\Velcro\DataModel;
use Archman\Velcro\Field;

// 该类拥有一个protected属性和一个private属性
class Foo extends DataModel
{
    #[Field('field1')]
    protected int $val1;
    
    #[Field('field2')]
    private int $val2;
    
    public function assertProps(int $v1, int $v2)
    {
        assert($this->val1 === $v1);
        assert($this->val2 === $v2);
    }
}

$foo = new Foo(['field1' => 1, 'field2' => 2]);
$foo->assertProps(1, 2);

```

