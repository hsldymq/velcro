<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\PropertyType;
use Attribute;

#[Attribute]
class DateTimeConverter implements ConverterInterface
{
    /** @var int ISO-8601 格式 */
    const ISO_8601 = 0;

    /** @var int 时间戳 */
    const TIMESTAMP = 1;

    /** @var int 毫秒时间戳 */
    const TIMESTAMP_MS = 2;

    public function __construct(private int $type)
    {
        if (!in_array($this->type, [self::ISO_8601, self::TIMESTAMP, self::TIMESTAMP_MS])) {
            throw new \InvalidArgumentException('invalid type for DateTimeConverter');
        }
    }

    public function convert(mixed $fieldValue, PropertyType $propertyType): \DateTime
    {
        return match ($this->type) {
            self::TIMESTAMP => new \DateTime("@{$fieldValue}"),
            self::TIMESTAMP_MS => new \DateTime("@".($fieldValue / 1000)),
            default => new \DateTime($fieldValue),
        };
    }
}