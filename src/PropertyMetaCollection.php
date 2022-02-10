<?php

declare(strict_types=1);

namespace Archman\Velcro;

class PropertyMetaCollection
{
    /** @var array<string, PropertyMeta> */
    private array $metaList = [];

    public function addPropertyMeta(PropertyMeta $meta)
    {
        $this->metaList[$meta->getPropertyName()] = $meta;
    }

    public function getPropertyMetaByName(string $propertyName): ?PropertyMeta
    {
        return $this->metaList[$propertyName] ?? null;
    }

    /**
     * @return \Generator|array<PropertyMeta>
     */
    public function iter(): \Generator
    {
        foreach ($this->metaList as $each) {
            yield $each;
        }
    }
}