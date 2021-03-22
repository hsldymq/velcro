<?php

namespace Archman\DataModel\Tests\Models;

use Archman\DataModel\DataModel;
use Archman\DataModel\Field;

class BasicModel extends DataModel
{
    #[Field('aa')]
    public int $a;

    #[Field('bb')]
    public string $b;

    #[Field('cc')]
    public bool $c;

    #[Field('dd')]
    protected float $d;

    #[Field('ee')]
    private array $e;

    public function getD(): float
    {
        return $this->d;
    }

    public function getE(): array
    {
        return $this->e;
    }
}
