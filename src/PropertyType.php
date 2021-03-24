<?php

declare(strict_types=1);

namespace Archman\DataModel;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class PropertyType
{
    /**
     * @var ReflectionNamedType[]
     */
    private array $namedTypes;

    private array $types;

    public function __construct(?ReflectionType $type, private string $className)
    {
        if (!$type) {
            // 没有类型声明即等价于mixed
            $this->namedTypes = [self::getMixedType()];
        }

        /** @var ReflectionNamedType|ReflectionUnionType $type */
        $this->namedTypes = match (true) {
            $type instanceof ReflectionNamedType => [$type],
            default => $type->getTypes(),
        };
    }

    /**
     * 是否是指定类型之一.
     *
     * @param array $types 可能的类型名: 'int' / 'float' / 'string' / 'bool' / 'array' / 'null' / 'object' / 'iterable' / 'self' / 'mixed' / '{Fully-Qualified Class Name}'
     *
     * @return bool
     */
    public function isOneOf(array $types): bool
    {
        $this->analyze();

        foreach ($types as $each) {
            if ($this->types[$each] ?? false) {
                return true;
            }
        }

        return false;
    }

    public function isNullable(): bool
    {
        $this->analyze();

        return $this->types['null'] ?? false;
    }

    public function isMixed(): bool
    {
        $this->analyze();

        return $this->types['mixed'] ?? false;
    }

    private function analyze(): void
    {
        if (isset($this->types)) {
            return;
        }

        $this->types = [];
        foreach ($this->namedTypes as $each) {
            $typeName = $each->getName();
            if ($typeName === 'false') {
                $this->types['bool'] = true;
                continue;
            }

            $this->types[$typeName] = true;
            if ($typeName === 'mixed' || $each->allowsNull()) {
                $this->types['null'] = true;
            } else if ($typeName === 'self') {
                $this->types[$this->className] = true;
            }
        }
    }

    private static function getMixedType(): ReflectionNamedType
    {
        /** @var ReflectionNamedType $mixedType */
        static $mixedType;

        if (!$mixedType) {
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
        }

        return $mixedType;
    }
}