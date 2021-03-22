<?php

declare(strict_types=1);

namespace Archman\DataModel;

use ReflectionNamedType;

class PropertyType
{
    private bool $nullable;

    private bool $mixed;

    /**
     * @param ReflectionNamedType[] $types
     */
    public function __construct(protected array $types)
    {
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