<?php

namespace app\database\library;

use ReflectionClass;

class Helpers
{
    public static function getClassShortName(object|string $class)
    {
        $reflect = new ReflectionClass($class);

        return $reflect->getShortName();
    }
}
