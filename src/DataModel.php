<?php

declare(strict_types=1);

namespace Archman\Velcro;

use Archman\Velcro\Converters\ConverterInterface;
use Archman\Velcro\Exceptions\ConversionException;
use Archman\Velcro\Exceptions\ReadonlyException;
use ReflectionClass;
use RuntimeException;
use Throwable;

abstract class DataModel
{
    /**
     * 缓存的DataModel子类解析信息. 当多次实例化同一个DataModel子类时, 只需要进行一次反射, 避免不必要的性能开销.
     *
     * @var array<string, PropertyMetaCollection>
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
            self::$classesInfo[$className] = $this->parseProps($className);
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

    final protected function getPropsInfo(): PropertyMetaCollection
    {
        return self::$classesInfo[$this->getClassName()];
    }

    private function parseProps(string $className): PropertyMetaCollection
    {
        $propertyMetaCollection = new PropertyMetaCollection();

        $obj = new ReflectionClass($className);
        $hasClassReadonlyAttr = ($obj->getAttributes(RO::class)[0] ?? null) !== null;
        foreach ($obj->getProperties() as $prop) {
            $isLanguageReadOnlyProp = version_compare(PHP_VERSION, '8.1.0', '>=') && $prop->isReadOnly();

            $fieldAttr = $prop->getAttributes(Field::class)[0] ?? null;
            if (!$fieldAttr) {
                continue;
            }

            $propName = $prop->getName();
            $fieldName = $fieldAttr->newInstance()->getFieldName();
            $property = new Property($prop, $fieldName);
            $converter = null;
            $setter = null;
            $legacyReadonly = $hasClassReadonlyAttr;
            foreach ($prop->getAttributes() as $each) {
                $attrName = $each->getName();
                if ($attrName === RO::class && $property->isPublic()) {
                    $legacyReadonly = true;
                } else if (is_subclass_of($attrName, ConverterInterface::class) && !$converter) {
                    try {
                        /** @var ConverterInterface $converter */
                        $converter = $each->newInstance();
                        $converter->bindToProperty($property);
                    } catch (Throwable $e) {
                        throw $this->makeConversionException($each->getName(), $propName, $e);
                    }
                }
            }
            if ($prop->isPrivate() || $isLanguageReadOnlyProp) {
                $setter = (function(string $propName) {
                    return function(mixed $value) use ($propName) {
                        $this->$propName = $value;
                    };
                })($propName);
            }
            if ($legacyReadonly && $isLanguageReadOnlyProp) {
                $legacyReadonly = false;
            }

            $propertyMetaCollection->addPropertyMeta(new PropertyMeta(
                $propName,
                $fieldName,
                $property,
                $converter,
                $setter,
                $legacyReadonly,
            ));
        }

        return $propertyMetaCollection;
    }

    private function doAssign()
    {
        foreach ($this->getPropsInfo()->iter() as $eachPropMeta) {
            $propName = $eachPropMeta->getPropertyName();
            $fieldName = $eachPropMeta->getFieldName();
            if (!array_key_exists($fieldName, $this->data)) {
                continue;
            }

            $value = $this->data[$fieldName];
            if ($converter = $eachPropMeta->getConverter()) {
                try {
                    $value = $converter->convert($value);
                } catch (Throwable $e) {
                    throw $this->makeConversionException(get_class($converter), $propName, $e);
                }
            }
            if ($setter = $eachPropMeta->getSetter()) {
                $setter->bindTo($this, $this)($value);
            } else {
                $this->$propName = $value;
            }
            if ($eachPropMeta->isLegacyReadonly()) {
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