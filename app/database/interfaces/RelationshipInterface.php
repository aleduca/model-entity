<?php

namespace app\database\interfaces;

use app\database\model\Model;

interface RelationshipInterface
{
    public function createWith(Model $class, string $foreignClass, ?string $withProperty):object;
}
