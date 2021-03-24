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

    private array $declaredTypes;

    public function __construct(?ReflectionType $type, private string $className)
    {
        if (!$type) {
            // 没有类型声明即等价于mixed
            $this->namedTypes = [TypeHelper::getMixedType()];
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
            if ($this->declaredTypes[$each] ?? false) {
                return true;
            }
        }

        return false;
    }

    public function isNullable(): bool
    {
        $this->analyze();

        return $this->declaredTypes['null'] ?? false;
    }

    public function isMixed(): bool
    {
        $this->analyze();

        return $this->declaredTypes['mixed'] ?? false;
    }

    private function analyze(): void
    {
        if (isset($this->declaredTypes)) {
            return;
        }

        $this->declaredTypes = TypeHelper::parseNamedTypes($this->namedTypes);
        if (isset($this->declaredTypes['self'])) {
            $this->declaredTypes[$this->className] = true;
        }
    }
}