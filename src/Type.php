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
    private array $types = [];

    /**
     * @var array [
     *      'isNullable' => <bool>,
     *      'isMixed' => <bool>,
     * ]
     */
    private array $typesInfo;

    public function __construct(?ReflectionType $type)
    {
        if (!$type) {
            // 没有类型声明即等价于mixed
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
            $this->types = [$mixedType];
        }

        $this->types = match (true) {
            $type instanceof ReflectionUnionType => $type->getTypes(),
            $type instanceof ReflectionNamedType => [$type],
            default => throw new \InvalidArgumentException('unknown reflection type')
        };
    }

    public function isNullable(): bool
    {
        $this->analysis();

        return $this->typesInfo['isNullable'] ?? false;
    }

    public function isMixed(): bool
    {
        $this->analysis();

        return $this->typesInfo['isMixed'] ?? false;
    }

    private function analysis(): void
    {
        if (isset($this->typesInfo)) {
            return;
        }

        foreach ($this->types as $each) {
            $typeName = $each->getName();

            if ($typeName === 'mixed') {
                $this->typesInfo['isMixed'] = true;
                $this->typesInfo['isNullable'] = true;
            } else if ($each->allowsNull() || $typeName === 'null') {
                $this->typesInfo['isNullable'] = true;
            }
        }
    }
}