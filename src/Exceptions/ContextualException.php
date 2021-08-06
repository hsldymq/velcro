<?php

declare(strict_types=1);

namespace Archman\Velcro\Exceptions;

use Exception;
use Throwable;

class ContextualException extends Exception
{
    public function __construct(protected array $context = [], $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getRootCause(): Throwable
    {
        $curr = $this;
        do {
            $rootCause = $curr;
        } while ($curr = $curr->getPrevious());

        return $rootCause;
    }
}