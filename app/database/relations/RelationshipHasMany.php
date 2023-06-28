<?php

namespace app\database\relations;

use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use Exception;

class RelationshipHasMany implements RelationshipInterface
{
    public function createWith(string $class, string $foreignClass, string $withProperty, array $results):object
    {
        if (!class_exists($foreignClass)) {
            throw new Exception("Model {$foreignClass} does not exist");
        }

        $classShortName = Helpers::getClassShortName($class);
        $classNameWithIdSuffix = strtolower($classShortName) . '_id';

        $ids = array_map(function ($data) {
            return $data->id;
        }, $results);

        $relatedWith = new $foreignClass;
        $resultsFromRelated = $relatedWith->relatedWith(array_unique($ids), $classNameWithIdSuffix);

        // $withName = (!$withProperty) ? strtolower($classShortName) : $withProperty;

        foreach ($results as $data) {
            // $data->$withName = [];
            $arrayOfData = [];
            foreach ($resultsFromRelated as $dataFromRelated) {
                if ($data->id === $dataFromRelated->$classNameWithIdSuffix) {
                    $arrayOfData[] = $dataFromRelated;
                    // $data->$withName[] = $dataFromRelated;
                }
            }
            $data->$withProperty = $arrayOfData;
        }

        return (object)[
            'items' => $results,
            'withName' => $withProperty,
        ];
    }
}
