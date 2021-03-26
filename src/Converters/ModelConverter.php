<?php

declare(strict_types=1);

namespace Archman\Velcro\Converters;

use Archman\Velcro\Model;
use Archman\Velcro\Property;
use Attribute;
use TypeError;

#[Attribute]
class ModelConverter implements ConverterInterface
{
    public function __construct(private string $modelClass = '')
    {
    }

    public function convert(mixed $fieldValue, Property $property): Model
    {
        if ($this->modelClass) {
            if (!is_subclass_of($this->modelClass, Model::class)) {
                throw new TypeError("{$this->modelClass} is not a subclass of DataModel");
            }
            $dataModelClass = $this->modelClass;
        } else if ($property->isDataModel()) {
            $dataModelClass = $property->getDataModelTypes()[0];
        } else {
            throw new TypeError("the type of the property is not a subclass of DataModel");
        }

        return new $dataModelClass($fieldValue);
    }
}