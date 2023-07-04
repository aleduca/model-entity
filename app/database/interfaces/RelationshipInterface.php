<?php

namespace app\database\interfaces;

use app\database\entity\Entity;

interface RelationshipInterface
{
    public function createWith(string $class, string $foreignClass, string $withProperty, array|Entity $results):object;
}
