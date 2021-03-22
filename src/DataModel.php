<?php

declare(strict_types=1);

namespace Archman\DataModel;

use Archman\DataModel\Converters\ConverterInterface;
use Closure;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

abstract class DataModel
{
    /**
     * @var array 缓存的DataModel子类解析信息. 当多次实例化同一个DataModel子类时, 只需要进行一次反射, 避免不必要的性能开销.
     *  [
     *      '{className}' => [                                          // 完整(含命名空间)的类名
     *          '{propertyName}' => [                                   // 定义了Field Attribute的属性
     *              'propType' => <PropertyType>                        // 属性的类型信息
     *              'field' => <string>,                                // 数据的字段名
     *              'converter' => <ConverterInterface>,                // 数据类型转换对象
     *              'assigner' => <Closure>,                            // 赋值器(用于对private属性进行赋值)
     *          ],
     *          ...
     *      ],
     *      ...
     *  ]
     */
    private static array $cachedClasses = [];

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
        if (isset(self::$cachedClasses[$modelClass])) {
            $this->cacheAssign($modelClass);
        } else {
            $this->reflectAssign($modelClass);
        }
    }

    private function reflectAssign(string $className)
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

            $propsInfo[$propName] = [
                'propType' => self::extractPropType($prop),
                'field' => $fieldName
            ];

            /** @var ConverterInterface $converter */
            $converter = null;
            foreach ($prop->getAttributes() as $each) {
                if (is_subclass_of($each->getName(), ConverterInterface::class)) {
                    $propsInfo[$propName]['converter'] = $converter = $each->newInstance();
                    break;
                }
            }

            $assigner = null;
            if ($prop->isPrivate()) {
                $propsInfo[$propName]['assigner'] = $assigner = (function(string $propName) {
                    return function(mixed $value) use ($propName) {
                        $this->$propName = $value;
                    };
                })($propName);
            }

            if (!isset($this->data[$fieldName])) {
                continue;
            }

            $value = $this->data[$fieldName];
            if ($converter) {
                $value = $converter->convert($value, $propsInfo[$propName]['propType']);
            }
            if ($assigner) {
                $assigner->bindTo($this, $this)($value);
            } else {
                $this->$propName = $value;
            }
        }
        self::$cachedClasses[$className] = $propsInfo;
    }

    private function cacheAssign(string $className)
    {
        foreach (self::$cachedClasses[$className] as $propName => $info) {
            $fieldName = $info['field'];
            if (!isset($this->data[$fieldName])) {
                continue;
            }

            $value = $this->data[$fieldName];
            /** @var ConverterInterface $converter */
            $converter = $info['converter'] ?? null;
            if ($converter) {
                $value = $converter->convert($value, $info['propType']);
            }
            /** @var Closure $assigner */
            $assigner = $info['assigner'] ?? null;
            if ($assigner) {
                $assigner->bindTo($this, $this)($value);
            } else {
                $this->$propName = $value;
            }
        }
    }

    /**
     * @param ReflectionProperty $prop
     * @return PropertyType
     * @throws
     */
    private static function extractPropType(ReflectionProperty $prop): PropertyType
    {
        $reflectionType = $prop->getType();
        if (!$reflectionType) {
            // 没有类型声明即等价于mixed
            $type = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
            return new PropertyType([$type]);
        }

        $namedTypes = match (true) {
            $reflectionType instanceof ReflectionUnionType => $reflectionType->getTypes(),
            $reflectionType instanceof ReflectionNamedType => [$reflectionType],
            default => throw new \InvalidArgumentException('unknown reflection type')
        };

        return new PropertyType($namedTypes);
    }
}