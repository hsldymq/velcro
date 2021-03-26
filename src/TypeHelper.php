<?php

declare(strict_types=1);

namespace Archman\Velcro;

use ReflectionNamedType;

class TypeHelper
{
    public static function getMixedType(): ReflectionNamedType
    {
        /** @var ReflectionNamedType $mixedType */
        static $mixedType;

        if (!$mixedType) {
            $mixedType = (new \ReflectionFunction(function (mixed $p) {}))->getParameters()[0]->getType();
        }

        return $mixedType;
    }

    public static function parseNamedTypes(array $types): array
    {
        $result = [];
        foreach ($types as $each) {
            $typeName = $each->getName();
            if ($typeName === 'false') {
                $result['bool'] = true;
                continue;
            }

            $result[$typeName] = true;
            if ($typeName === 'mixed' || $each->allowsNull()) {
                $result['null'] = true;
            }
        }

        return $result;
    }

    public static function getValueType(mixed $value): array
    {
        static $map = [
            'integer' => 'int',
            'double' => 'float',
            'boolean' => 'bool',
        ];

        $isObject = false;
        $type = strtolower(gettype($value));
        $type = $map[$type] ?? $type;
        if ($type === 'object') {
            $type = get_class($value);
            $isObject = true;
        }

        return [$type, $isObject];
    }
}