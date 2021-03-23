<?php

declare(strict_types=1);

namespace Archman\DataModel;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class Type
{
    /**
     * @var ReflectionNamedType[]
     */
    private array $namedTypes = [];

    private array $types;

    public function __construct(?ReflectionType $type)
    {
        if (!$type) {
            // 没有类型声明即等价于mixed
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
            $this->namedTypes = [$mixedType];
        }

        $this->namedTypes = match (true) {
            $type instanceof ReflectionUnionType => $type->getTypes(),
            $type instanceof ReflectionNamedType => [$type],
            default => throw new \InvalidArgumentException('unknown reflection type')
        };
    }

    /**
     * @return ReflectionNamedType[]
     */
    public function getNamedTypes(): array
    {
        return $this->namedTypes;
    }

    public function isNullable(): bool
    {
        $this->analysis();

        return $this->types['null'] ?? false;
    }

    public function isMixed(): bool
    {
        $this->analysis();

        return $this->types['mixed'] ?? false;
    }

    private function analysis(): void
    {
        if (isset($this->types)) {
            return;
        }

        $this->types = [];
        foreach ($this->namedTypes as $each) {
            $typeName = $each->getName();

            if ($typeName === 'mixed') {
                $this->types['mixed'] = true;
                $this->types['null'] = true;
            } else if ($each->allowsNull() || $typeName === 'null') {
                $this->types['null'] = true;
            } else {
                $this->types[$typeName] = true;
            }
        }
    }
}