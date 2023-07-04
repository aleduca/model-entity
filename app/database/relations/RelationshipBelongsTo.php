<?php

namespace app\database\relations;

use app\database\entity\Entity;
use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use Exception;

class RelationshipBelongsTo implements RelationshipInterface
{
    public function createWith(string $class, string $foreignClass, string $withProperty, array|Entity $results):object
    {
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        $classShortName = Helpers::getClassShortName($foreignClass);
        $foreignKey = strtolower($classShortName) . '_id';

        if (is_array($results)) {
            $ids = array_map(function ($data) use ($foreignKey) {
                return $data->$foreignKey;
            }, $results);
        }

        if ($results instanceof Entity) {
            $ids = $results->$foreignKey;
        }

        $relatedWith = new $foreignClass;
        $resultsFromRelated = $relatedWith->relatedWith(is_array($ids) ? array_unique($ids) : $ids);


        if ($results instanceof Entity) {
            $results->$withProperty = $resultsFromRelated[0];
        } else {
            foreach ($results as $data) {
                foreach ($resultsFromRelated as $dateFromRelated) {
                    if ($data->$foreignKey === $dateFromRelated->id) {
                        $data->$withProperty = $dateFromRelated;
                    }
                }
            }
        }

        return (object)[
            'items' => $results,
            'withName' => $withProperty,
        ];
    }
}
