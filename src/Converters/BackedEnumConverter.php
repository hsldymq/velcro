<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\Property;
use Attribute;

/**
 * 这个Converter用于从整型和字符串恢复为回退枚举(backed enumerations).
 *
 * @see https://www.php.net/manual/en/language.enumerations.backed.php
 */
#[Attribute]
class BackedEnumConverter implements ConverterInterface
{
    private Property $boundProperty;
    private \ReflectionEnum $reflectionType;

    public function __construct(private \BackedEnum|null $default = null)
    {
    }

    public function bindToProperty(Property $property)
    {
        $this->reflectionType = new \ReflectionEnum($property->getType()->getName());
        if (!$this->reflectionType->isBacked()) {
            throw new \InvalidArgumentException('property is not a backed enum');
        }

        $this->boundProperty = $property;
    }

    public function convert(mixed $fieldValue): \BackedEnum
    {
        $method = $this->reflectionType->getMethod('tryFrom');
        $e = $method->invoke(null, $fieldValue);
        if (!$e) {
            if ($this->default === null) {
                throw new \InvalidArgumentException('can not recover backed enum from value');
            }
            $e = $this->default;
        }

        return $e;
    }
}