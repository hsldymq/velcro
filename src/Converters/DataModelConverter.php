<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\DataModel;
use Archman\DataModel\Property;
use Attribute;
use TypeError;

#[Attribute]
class DataModelConverter implements ConverterInterface
{
    public function __construct(private string $modelClass = '')
    {
    }

    public function convert(mixed $fieldValue, Property $property): DataModel
    {
        if ($this->modelClass) {
            if (!is_subclass_of($this->modelClass, DataModel::class)) {
                throw new TypeError("DataModelConverter: {$property->getClassName()}::\${$property->getPropertyName()}, {$this->modelClass} is not a subclass of DataModel");
            }
            $dataModelClass = $this->modelClass;
        } else if ($property->isDataModel()) {
            $dataModelClass = $property->getDataModelTypes()[0];
        } else {
            throw new TypeError("DataModelConverter: the type of {$property->getClassName()}::\${$property->getPropertyName()} is not a subclass of DataModel");
        }

        return new $dataModelClass($fieldValue);
    }
}