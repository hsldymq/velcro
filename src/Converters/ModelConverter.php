<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\DataModel;
use Archman\Velcro\Property;
use Attribute;
use TypeError;

#[Attribute]
class ModelConverter implements ConverterInterface
{
    private Property $boundProperty;

    public function __construct(private string $modelClass = '')
    {
    }

    public function bindToProperty(Property $property)
    {
        if ($this->modelClass) {
            if (!is_subclass_of($this->modelClass, DataModel::class)) {
                throw new TypeError("{$this->modelClass} is not a subclass of DataModel");
            }
        } else if (!$property->isDataModel()) {
            throw new TypeError("the type of the property is not a subclass of DataModel");
        } else {
            $this->modelClass = $property->getDataModelTypes()[0];
        }

        $this->boundProperty = $property;
    }

    public function convert(mixed $fieldValue): DataModel
    {
        $dataModelClass = $this->modelClass;

        return new $dataModelClass($fieldValue);
    }
}