<?php

declare(strict_types=1);

namespace Archman\Velcro\Exceptions;

use Throwable;

class ReadonlyException extends ContextualException
{
    private string $className;
    private string $propertyName;

    public function __construct(array $context = [], $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($context, $message, $code, $previous);

        $this->className = $context['className'] ?? '';
        $this->propertyName = $context['propertyName'] ?? '';
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }
}