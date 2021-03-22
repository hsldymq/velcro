<?php

declare(strict_types=1);

namespace Archman\DataModel;

use Archman\DataModel\Attributes\Converter;
use Archman\DataModel\Attributes\Field;
use Archman\DataModel\Converters\ConverterInterface;
use Closure;

abstract class DataModel
{
    /**
     * @var array [
     *      '{className}' => [
     *          '{propertyName}' => [
     *              'field' => '{dataFieldName <string>}',
     *              'converter' => {converter <ConverterInterface>},
     *              'assigner' => {assigner <Closure>},
     *          ],
     *      ],
     *      ...
     * ]
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

            /** @var Converter $converterAttr */
            $converterAttr = ($prop->getAttributes(Converter::class)[0] ?? null)?->newInstance() ?? null;
            $converter = null;
            if ($converterAttr) {
                foreach ($prop->getAttributes($converterAttr->getConverterClass()) as $eachAttr) {
                    $converter = $eachAttr->newInstance();
                    break;
                }
            }

            $propsInfo[$propName] = ['field' => $fieldName];
            if ($converter) {
                $propsInfo[$propName]['converter'] = $converter;
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
                $value = $converter->convert($value);
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
                $value = $converter->convert($value);
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
}