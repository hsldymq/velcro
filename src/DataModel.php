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

abstract class DataModel
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
     *              'legacyReadonly' => <bool>,                         // 是否是只读 (8.0或8.1以上没标记readonly关键字的情况下设置)
     *          ],
     *          ...
     *      ],
     *      ...
     *  ]
     */
    private static array $classesInfo = [];

    private array $readonlyPropsVal = [];

    private array $signedFieldNames = [];

    private string $className;

    public function __construct(private array $data = [])
    {
        $this->assignProps();
    }

    /**
     * 返回未分配给属性的数据字段名列表.
     *
     * @return array
     */
    public function getUnsignedFieldNames(): array
    {
        $data = $this->getRawData();
        foreach ($this->signedFieldNames as $eachField) {
            if (array_key_exists($eachField, $this->data)) {
                unset($data[$eachField]);
            }
        }

        return array_keys($data);
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
            $errStr = "Undefined property: {$this->getClassName()}::\${$name}";
        } else {
            $errStr = "Cannot access non-public property {$this->getClassName()}::\${$name}";
        }
        throw new RuntimeException($errStr);
    }

    public function __set(string $name, mixed $value)
    {
        $this->ensurePropWritable($name);

        if (property_exists($this, $name)) {
            throw new RuntimeException("Cannot access non-public property {$this->getClassName()}::\${$name}");
        }

        $this->$name = $value;
    }

    final protected function assignProps()
    {
        $className = $this->getClassName();
        if (!isset(self::$classesInfo[$className])) {
            self::$classesInfo[$className] = $this->parsePropsInfo($className);
        }
        $this->doAssign();
    }

    final protected function getReadonlyPropValue(string $propName): array
    {
        if (array_key_exists($propName, $this->readonlyPropsVal)) {
            return [$this->readonlyPropsVal[$propName], true];
        }

        return [null, false];
    }

    final protected function ensurePropWritable(string $propName)
    {
        if (array_key_exists($propName, $this->readonlyPropsVal)) {
            $className = $this->getClassName();
            throw new ReadonlyException([
                'className' => $className,
                'propertyName' => $propName,
            ], "Cannot set readonly property {$className}::\${$propName}");
        }
    }

    final protected function getPropsInfo(): array
    {
        return self::$classesInfo[$this->getClassName()];
    }

    private function parsePropsInfo(string $className): array
    {
        $propsInfo = [];
        $obj = new ReflectionClass($className);
        $hasClassReadonlyAttr = ($obj->getAttributes(RO::class)[0] ?? null) !== null;

        foreach ($obj->getProperties() as $prop) {
            $propName = $prop->getName();
            $isReadOnlyProp = version_compare(PHP_VERSION, '8.1.0', '>=') ? $prop->isReadOnly() : false;

            $fieldAttr = $prop->getAttributes(Field::class)[0] ?? null;
            if (!$fieldAttr) {
                continue;
            }

            $legacyReadonly = $hasClassReadonlyAttr;
            $fieldName = $fieldAttr->newInstance()->getFieldName();
            $property = new Property($prop, $fieldName);
            $propInfo = [
                'property' => $property,
                'fieldName' => $fieldName,
                'converter' => null,
                'setter' => null,
                'legacyReadonly' => false,
            ];
            foreach ($prop->getAttributes() as $each) {
                $attrName = $each->getName();
                if ($attrName === RO::class && $property->isPublic()) {
                    $legacyReadonly = true;
                } else if (is_subclass_of($attrName, ConverterInterface::class) && !$propInfo['converter']) {
                    try {
                        /** @var ConverterInterface $converter */
                        $converter = $each->newInstance();
                        $converter->bindToProperty($property);
                        $propInfo['converter'] = $converter;
                    } catch (Throwable $e) {
                        throw $this->makeConversionException($each->getName(), $propName, $e);
                    }
                }
            }
            if ($prop->isPrivate() || $isReadOnlyProp) {
                $propInfo['setter'] = (function(string $propName) {
                    return function(mixed $value) use ($propName) {
                        $this->$propName = $value;
                    };
                })($propName);
            }
            if ($legacyReadonly && $isReadOnlyProp) {
                $legacyReadonly = false;
            }
            $propInfo['legacyReadonly'] = $legacyReadonly;

            $propsInfo[$propName] = $propInfo;
        }

        return $propsInfo;
    }

    private function doAssign()
    {
        foreach ($this->getPropsInfo() as $propName => $info) {
            $fieldName = $info['fieldName'];
            if (!array_key_exists($fieldName, $this->data)) {
                continue;
            }

            $value = $this->data[$fieldName];
            /** @var ConverterInterface $converter */
            if ($converter = $info['converter']) {
                try {
                    $value = $converter->convert($value);
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
            if ($info['legacyReadonly']) {
                $this->readonlyPropsVal[$propName] = $this->$propName;
                unset($this->$propName);
            }
            $this->signedFieldNames[] = $fieldName;
        }
    }

    private function getClassName(): string
    {
        if (!isset($this->className)) {
            $this->className = get_class($this);
        }

        return $this->className;
    }

    private function makeConversionException(string $converterClassName, string $propName, Throwable $e): ConversionException
    {
        if ($e instanceof ConversionException) {
            return $e;
        }

        throw new ConversionException([
            'className' => $this->className,
            'propertyName' => $propName,
            'converterClassName' => $converterClassName
        ], "conversion error({$this->className}::\${$propName}): {$e->getMessage()}", 0, $e);
    }
}