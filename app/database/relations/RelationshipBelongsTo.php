<?php

namespace app\database\relations;

use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use Exception;

class RelationshipBelongsTo implements RelationshipInterface
{
    public function createWith(string $class, string $foreignClass, ?string $withProperty)
    {
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        $modelClass = new $class;
        $results = $modelClass->all();

        $classShortName = Helpers::getClassShortName($foreignClass);
        $foreignKey = strtolower($classShortName) . '_id';

        $ids = array_map(function ($data) use ($foreignKey) {
            return $data->$foreignKey;
        }, $results);

        $relatedWith = new $foreignClass;
        $resultsFromRelated = $relatedWith->relatedWith(array_unique($ids));

        $withName = (!$withProperty) ? strtolower($classShortName) : $withProperty;

        foreach ($results as $data) {
            foreach ($resultsFromRelated as $dateFromRelated) {
                if ($data->$foreignKey === $dateFromRelated->id) {
                    $data->$withName = $dateFromRelated;
                }
            }
        }

        return $results;
    }
}
