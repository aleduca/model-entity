<?php

namespace app\database\interfaces;

interface RelationshipInterface
{
    public function createWith(string $class, string $foreignClass, string $withProperty, array $results):object;
}
