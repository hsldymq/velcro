<?php

declare(strict_types=1);

namespace Archman\DataModel\Converters;

use Archman\DataModel\DataModel;
use Archman\DataModel\PropertyType;
use Attribute;
use TypeError;

#[Attribute]
class DataModelConverter implements ConverterInterface
{
    public function __construct(private string $dataModelSubclass = '')
    {
        if ($this->dataModelSubclass && !is_subclass_of($this->dataModelSubclass, DataModel::class)) {
            throw new TypeError('DataModelConverter: specified class should be a subclass of DataModel');
        }
    }

    public function convert(mixed $fieldValue, PropertyType $type): DataModel
    {
        if ($this->dataModelSubclass) {
            $dataModelClass = $this->dataModelSubclass;
        } else if ($type->isDataModel()) {
            $dataModelClass = $type->getDataModelTypes()[0];
        } else {
            throw new TypeError('DataModelConverter: the property should be a subclass of DataModel');
        }

        return new $dataModelClass($fieldValue);
    }
}