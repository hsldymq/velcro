<?php

declare(strict_types=1);

namespace Archman\DataModel;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class Property
{
    /**
     * @var ReflectionNamedType[]
     */
    private array $namedTypes;

    private array $declaredTypes;

    private array $dataModelTypes = [];

    public function __construct(private string $className, private string $propertyName, ?ReflectionType $type)
    {
        if (!$type) {
            // 没有类型声明即等价于mixed
            $this->namedTypes = [TypeHelper::getMixedType()];
            return;
        }

        /** @var ReflectionNamedType|ReflectionUnionType $type */
        $this->namedTypes = match (true) {
            $type instanceof ReflectionNamedType => [$type],
            default => $type->getTypes(),
        };
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
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

    /**
     * 返回DataModel子类的声明类型列表.
     *
     * @return array
     */
    public function getDataModelTypes(): array
    {
        return $this->dataModelTypes;
    }

    /**
     * 是否是DataModel子类.
     *
     * @return bool
     */
    public function isDataModel(): bool
    {
        $this->analyze();

        return !empty($this->dataModelTypes);
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

        foreach ($this->declaredTypes as $typeName => $_) {
            if (is_subclass_of($typeName, DataModel::class)) {
                $this->dataModelTypes[] = $typeName;
            }
        }
    }
}