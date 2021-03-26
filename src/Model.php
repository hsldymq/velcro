<?php

declare(strict_types=1);

namespace Archman\Velcro;

use Archman\Velcro\Converters\ConverterInterface;
use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Exceptions\ReadonlyException;
use Closure;
use ReflectionClass;
use RuntimeException;
use Throwable;

abstract class Model
{
    /**
     * @var array 缓存的DataModel子类解析信息. 当多次实例化同一个DataModel子类时, 只需要进行一次反射, 避免不必要的性能开销.
     *  [
     *      $className => [                                             // 完整(含命名空间)的类名
     *          $propName => [                                          // 定义了Field Attribute的属性
     *              'property' => <Property>                            // 属性信息
     *              'fieldName' => <string>,                            // 数据的字段名
     *              'converter' => <ConverterInterface|null>,           // 数据转换器
     *              'setter' => <Closure|null>,                         // 用于对private属性进行赋值
     *              'readonly' => <bool>,                               // 是否是只读
     *          ],
     *          ...
     *      ],
     *      ...
     *  ]
     */
    private static array $classesInfo = [];

    private string $className;

    private array $readonlyPropsVal = [];

    public function __construct(private array $data = [])
    {
        $this->assignProps();
    }

    public function getRawData(): array
    {
        return $this->data;
    }

    public function __get(string $name)
    {
        [$value, $exists] = $this->getReadonlyPropValue($name);
        if ($exists) {
            return $value;
        }

        if (!property_exists($this, $name)) {
            $errStr = "Undefined property: {$this->className}::\${$name}";
        } else {
            $errStr = "Cannot access non-public property {$this->className}::\${$name}";
        }
        throw new RuntimeException($errStr);
    }

    public function __set(string $name, mixed $value)
    {
        $this->ensurePropIsNotReadonly($name);

        if (property_exists($this, $name)) {
            throw new RuntimeException("Cannot access non-public property {$this->className}::\${$name}");
        }

        $this->$name = $value;
    }

    final protected function assignProps()
    {
        $this->className = get_class($this);
        if (!isset(self::$classesInfo[$this->className])) {
            self::$classesInfo[$this->className] = $this->parsePropsInfo($this->className);
        }
        $this->doAssign($this->className);
    }

    final protected function getReadonlyPropValue(string $propName): array
    {
        if (array_key_exists($propName, $this->readonlyPropsVal)) {
            return [$this->readonlyPropsVal[$propName], true];
        }

        return [null, false];
    }

    final protected function ensurePropIsNotReadonly(string $propName)
    {
        if (array_key_exists($propName, $this->readonlyPropsVal)) {
            throw new ReadonlyException([
                'className' => $this->className,
                'propertyName' => $propName,
            ], "Cannot set readonly property {$this->className}::\${$propName}");
        }
    }

    private function parsePropsInfo(string $className): array
    {
        $propsInfo = [];
        $obj = new ReflectionClass($className);
        foreach ($obj->getProperties() as $prop) {
            $propName = $prop->getName();

            $fieldAttr = $prop->getAttributes(Field::class)[0] ?? null;
            if (!$fieldAttr) {
                continue;
            }

            $property = new Property($prop);
            $propInfo = [
                'property' => $property,
                'fieldName' => $fieldAttr->getArguments()[0],
                'converter' => null,
                'setter' => null,
                'readonly' => false,
            ];
            foreach ($prop->getAttributes() as $each) {
                $attrName = $each->getName();
                if ($attrName === Readonly::class && $property->isPublic()) {
                    $propInfo['readonly'] = true;
                } else if (!$propInfo['converter'] && is_subclass_of($attrName, ConverterInterface::class)) {
                    try {
                        $propInfo['converter'] = $each->newInstance();
                    } catch (Throwable $e) {
                        throw $this->makeConversionException($each->getName(), $propName, $e);
                    }
                }
            }
            if ($prop->isPrivate()) {
                $propInfo['setter'] = (function(string $propName) {
                    return function(mixed $value) use ($propName) {
                        $this->$propName = $value;
                    };
                })($propName);
            }

            $propsInfo[$propName] = $propInfo;
        }

        return $propsInfo;
    }

    private function doAssign(string $className)
    {
        foreach (self::$classesInfo[$className] as $propName => $info) {
            $fieldName = $info['fieldName'];
            if (!array_key_exists($fieldName, $this->data)) {
                continue;
            }
            /** @var Property $prop */
            $prop = $info['property'];

            $value = $this->data[$fieldName];
            /** @var ConverterInterface $converter */
            if ($converter = $info['converter']) {
                try {
                    $value = $converter->convert($value, $prop);
                } catch (Throwable $e) {
                    throw $this->makeConversionException(get_class($converter), $propName, $e);
                }
            }
            /** @var Closure $setter */
            if ($setter = $info['setter']) {
                $setter->bindTo($this, $this)($value);
            } else {
                $this->$propName = $value;
            }
            if ($info['readonly']) {
                $this->readonlyPropsVal[$propName] = $this->$propName;
                unset($this->$propName);
            }
        }
    }

    private function makeConversionException(string $converterClassName, string $propName, Throwable $e): ConversionException
    {
        throw new ConversionException([
            'className' => $this->className,
            'propertyName' => $propName,
            'converterClassName' => $converterClassName
        ], "conversion error({$this->className}::\${$propName}): {$e->getMessage()}", previous: $e);
    }
}