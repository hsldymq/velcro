<?php

declare(strict_types=1);

namespace Archman\DataModel;

use Archman\DataModel\Converters\ConverterInterface;
use Closure;
use Throwable;

abstract class DataModel
{
    /**
     * @var array 缓存的DataModel子类解析信息. 当多次实例化同一个DataModel子类时, 只需要进行一次反射, 避免不必要的性能开销.
     *  [
     *      $className => [                                             // 完整(含命名空间)的类名
     *          $propertyName => [                                      // 定义了Field Attribute的属性
     *              'property' => <Property>                            // 属性信息
     *              'dataField' => <string>,                            // 数据的字段名
     *              'converter' => <ConverterInterface>,                // 数据类型转换对象
     *              'setter' => <Closure>,                              // 用于对private属性进行赋值
     *          ],
     *          ...
     *      ],
     *      ...
     *  ]
     */
    private static array $classesInfo = [];

    public function __construct(private array $data = [])
    {
        $this->assignProps();
    }

    public function toArray(): array
    {
        return $this->data;
    }

    final protected function assignProps()
    {
        $modelClass = get_class($this);
        if (!isset(self::$classesInfo[$modelClass])) {
            self::$classesInfo[$modelClass] = $this->parseInfo($modelClass);
        }
        $this->doAssign($modelClass);
    }

    private function parseInfo(string $className): array
    {
        $propsInfo = [];
        $obj = new \ReflectionObject($this);
        foreach ($obj->getProperties() as $prop) {
            $propName = $prop->getName();

            $attr = $prop->getAttributes(Field::class)[0] ?? null;
            $fieldName = $attr?->getArguments()[0] ?? null;
            if (!$fieldName) {
                continue;
            }

            $info = [
                'property' => new Property($className, $propName, $prop->getType()),
                'dataField' => $fieldName,
                'converter' => null,
                'setter' => null,
            ];
            foreach ($prop->getAttributes() as $each) {
                if (is_subclass_of($each->getName(), ConverterInterface::class)) {
                    try {
                        $info['converter'] = $each->newInstance();
                    } catch (Throwable $e) {
                        throw $this->makeConversionException($each->getName(), $e, $info['property']);
                    }
                    break;
                }
            }
            if ($prop->isPrivate()) {
                $info['setter'] = (function(string $propName) {
                    return function(mixed $value) use ($propName) {
                        $this->$propName = $value;
                    };
                })($propName);
            }

            $propsInfo[$propName] = $info;
        }

        return $propsInfo;
    }

    private function doAssign(string $className)
    {
        foreach (self::$classesInfo[$className] as $propName => $info) {
            $fieldName = $info['dataField'];
            if (!array_key_exists($fieldName, $this->data)) {
                continue;
            }

            $value = $this->data[$fieldName];
            /** @var ConverterInterface $converter */
            if ($converter = $info['converter']) {
                try {
                    $value = $converter->convert($value, $info['property']);
                } catch (\Throwable $e) {
                    throw $this->makeConversionException(get_class($converter), $e, $info['property']);
                }
            }
            /** @var Closure $setter */
            if ($setter = $info['setter']) {
                $setter->bindTo($this, $this)($value);
            } else {
                $this->$propName = $value;
            }
        }
    }

    private function makeConversionException(string $converterClassName, Throwable $e, Property $prop): ConversionException
    {
        throw new ConversionException([
            'className' => $prop->getClassName(),
            'propertyName' => $prop->getPropertyName(),
            'converterClassName' => $converterClassName
        ], "conversion error({$prop->getClassName()}::\${$prop->getPropertyName()}): {$e->getMessage()}", previous: $e);
    }
}