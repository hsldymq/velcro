<?php

declare(strict_types=1);

namespace Archman\Velcro\Tests\Models;

enum SimpleBackedEnum: int
{
    case V1 = 1;
    case V2 = 2;
    case Unknown = 3;
}