<?php

declare(strict_types=1);

namespace Archman\DataModel;

use Archman\DataModel\Attributes\Converter;
use Archman\DataModel\Attributes\Field;
use Archman\DataModel\Converters\ConverterInterface;

abstract class DataModel
{
    private static array $cachedClasses = [];

    public function __construct(protected array $data = [])
    {
        $this->assignProps();
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function assignProps()
    {
        $modelClass = get_class($this);
        [$propsInfo, $cached] = [self::$cachedClasses[$modelClass] ?? [], isset(self::$cachedClasses[$modelClass])];

        $obj = new \ReflectionObject($this);
        if ($cached) {
            foreach ($propsInfo as $propName => $info) {
                $value = $this->data[$info['field']];
                /** @var ConverterInterface $converter */
                $converter = $info['converter'] ?? null;
                if ($converter) {
                    $value = $converter->convert($value);
                }
                $this->$propName = $value;
            }
        } else {
            foreach ($obj->getProperties() as $prop) {
                $attr = $prop->getAttributes(Field::class)[0] ?? null;
                $fieldName = $attr?->getArguments()[0] ?? null;
                if (!$fieldName || !isset($this->data[$fieldName])) {
                    continue;
                }
                $value = $this->data[$fieldName];

                /** @var Converter $converterAttr */
                $converterAttr = ($prop->getAttributes(Converter::class)[0] ?? null)?->newInstance() ?? null;
                $converter = null;
                if ($converterAttr) {
                    foreach ($prop->getAttributes($converterAttr->getConverterClass()) as $eachAttr) {
                        $converter = $eachAttr->newInstance();
                        $value = $converter->convert($value);
                        break;
                    }
                }
                $prop->setValue($this, $value);

                $propName = $prop->getName();
                $propsInfo[$propName] = ['field' => $fieldName];
                if ($converter) {
                    $propsInfo[$propName]['converter'] = $converter;
                }
            }
            self::$cachedClasses[$modelClass] = $propsInfo;
        }

    }
}