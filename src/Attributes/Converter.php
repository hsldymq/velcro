<?php

declare(strict_types=1);

namespace Archman\DataModel\Attributes;

use Archman\DataModel\Converters\ConverterInterface;
use Attribute;

#[Attribute]
class Converter
{
    public function __construct(private string $converterClass)
    {
        self::checkClass($this->converterClass);
    }

    public function getConverterClass(): string
    {
        return $this->converterClass;
    }

    public static function checkClass(string $className): void
    {
        if (!is_subclass_of($className, ConverterInterface::class)) {
            throw new \InvalidArgumentException('class {$className} should implements DataConvertInterface');
        }
    }
}