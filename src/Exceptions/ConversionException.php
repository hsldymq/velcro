<?php

declare(strict_types=1);

namespace Archman\Velcro\Exceptions;

use Exception;
use Throwable;

class ConversionException extends Exception
{
    private string $className;
    private string $propertyName;
    private string $converterClassName;

    public function __construct(private Throwable $rootCause, private array $context = [], $message = "", $code = 0)
    {
        parent::__construct($message, $code, $rootCause);

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

    public function getRootCause(): Throwable
    {
        return $this->rootCause;
    }
}