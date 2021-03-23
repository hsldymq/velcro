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
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
            $this->namedTypes = [$mixedType];
        }

        /** @var ReflectionNamedType|ReflectionUnionType $type */
        $this->namedTypes = match (true) {
            $type instanceof ReflectionNamedType => [$type],
            default => $type->getTypes(),
        };
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
            $this->types[$typeName] = true;

            if ($typeName === 'mixed' || $each->allowsNull()) {
                $this->types['null'] = true;
            } else if ($typeName === 'self') {
                $this->types[$this->className] = true;
            }
        }
    }
}