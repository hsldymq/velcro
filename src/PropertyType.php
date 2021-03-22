<?php

declare(strict_types=1);

namespace Archman\DataModel;

use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class PropertyType
{
    /**
     * @var ReflectionNamedType[]
     */
    private array $types = [];

    private bool $nullable;

    private bool $mixed;

    public function __construct(private ReflectionProperty $prop)
    {
        $reflectionType = $prop->getType();
        if (!$reflectionType) {
            // 没有类型声明即等价于mixed
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
            $this->types = [$mixedType];
        }

        $this->types = match (true) {
            $reflectionType instanceof ReflectionUnionType => $reflectionType->getTypes(),
            $reflectionType instanceof ReflectionNamedType => [$reflectionType],
            default => throw new \InvalidArgumentException('unknown reflection type')
        };
    }

    public function isNullable(): bool
    {
        if (!isset($this->nullable)) {
            $this->analysis();
        }

        return $this->nullable;
    }

    public function isMixed(): bool
    {
        if (!isset($this->mixed)) {
            $this->analysis();
        }

        return $this->mixed;
    }

    private function analysis(): void
    {
        foreach ($this->types as $each) {
            $typeName = $each->getName();

            if ($typeName === 'mixed') {
                $this->mixed = true;
                $this->nullable = true;
            } else if ($each->allowsNull() || $typeName === 'null') {
                $this->nullable = true;
            }
        }
    }
}