<?php

namespace app\database\relations;

use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use app\database\model\Model;
use Exception;

class RelationshipBelongsTo implements RelationshipInterface
{
    public function createWith(Model $class, string $foreignClass, ?string $withProperty):object
    {
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        $results = $class->all();

        $classShortName = Helpers::getClassShortName($foreignClass);
        $foreignKey = strtolower($classShortName) . '_id';

        $ids = array_map(function ($data) use ($foreignKey) {
            return $data->$foreignKey;
        }, $results);

        $relatedWith = new $foreignClass;
        $resultsFromRelated = $relatedWith->relatedWith(array_unique($ids));


        foreach ($results as $data) {
            foreach ($resultsFromRelated as $dateFromRelated) {
                if ($data->$foreignKey === $dateFromRelated->id) {
                    $data->$withProperty = $dateFromRelated;
                }
            }
        }

        return (object)[
            'items' => $results,
            'withName' => $withProperty,
        ];
    }
}
