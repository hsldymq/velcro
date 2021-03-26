<?php

declare(strict_types=1);

namespace Archman\DataModel\Exceptions;

use Exception;
use Throwable;

class ConversionException extends Exception
{
    private string $className;
    private string $propertyName;
    private string $converterClassName;

    public function __construct(private array $context = [], $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->className = $context['className'] ?? '';
        $this->propertyName = $context['propertyName'] ?? '';
        $this->converterClassName = $context['converterClassName'] ?? '';
    }

    public function getContext(): array
    {
        return $this->context;
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