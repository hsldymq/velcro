<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\Property;
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

    private Property $boundProperty;

    public function __construct(private int $type)
    {
    }

    public function bindToProperty(Property $property)
    {
        $this->boundProperty = $property;
    }

    public function convert(mixed $fieldValue): \DateTime
    {
        return match ($this->type) {
            self::TIMESTAMP => new \DateTime("@{$fieldValue}"),
            self::TIMESTAMP_MS => new \DateTime("@".($fieldValue / 1000)),
            default => new \DateTime($fieldValue),
        };
    }
}