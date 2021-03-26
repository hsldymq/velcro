<?php

namespace Archman\Velcro\Tests\Models;

use Archman\Velcro\Model;
use Archman\Velcro\Field;
use Archman\Velcro\Readonly;

class BasicModel extends Model
{
    #[Field('aa')]
    public int $a;

    /**
     * @var string 只读, 试图修改该属性会抛出异常
     */
    #[Field('ro'), Readonly]
    public string $ro;

    #[Field('cc')]
    public bool $c;

    #[Field('dd')]
    protected float $d;

    /**
     * @var array 对非public属性,Readonly会被忽视
     */
    #[Field('ee'), Readonly]
    private array $e;

    #[Field('ff')]
    public ?int $f;

    #[Field('gg')]
    public mixed $g;

    public function getD(): float
    {
        return $this->d;
    }

    public function getE(): array
    {
        return $this->e;
    }
}
