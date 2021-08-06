<?php

declare(strict_types=1);

namespace Archman\Velcro\Exceptions;

use Throwable;

class ConversionException extends ContextualException
{
    private string $className;
    private string $propertyName;
    private string $converterClassName;

    public function __construct(array $context = [], $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($context, $message, $code, $previous);

        $this->className = $context['className'] ?? '';
        $this->propertyName = $context['propertyName'] ?? '';
        $this->converterClassName = $context['converterClassName'] ?? '';
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getConverterClassName(): string
    {
        return $this->converterClassName;
    }
}