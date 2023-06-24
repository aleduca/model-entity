<?php

namespace app\database\model;

use app\database\Connection;
use app\database\entity\Entity;
use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use app\database\relations\RelationshipBelongsTo;
use Exception;
use PDO;

abstract class Model
{
    protected string $table;

    private function getEntity()
    {
        $class = Helpers::getClassShortName(static::class);
        $entity = "app\\database\\entity\\{$class}Entity";

        if (!class_exists($entity)) {
            throw new Exception("Entity {$entity} does not exist");
        }

        return $entity;
    }

    public function all(string $fields = '*')
    {
        try {
            $connection = Connection::getConnection();
            $query = "select {$fields} from {$this->table}";
            $stmt = $connection->query($query);

            return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    public function create(Entity $entity)
    {
        try {
            $connection = Connection::getConnection();
            $query = "insert into {$this->table}(";
            $query .= implode(',', array_keys($entity->getAttributes())) . ') values(';
            $query .= ':' . implode(',:', array_keys($entity->getAttributes())) . ')';

            $prepare = $connection->prepare($query);

            return $prepare->execute($entity->getAttributes());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    private function relation(string $class, string $relation, ?string $property)
    {
        if (!class_exists($class)) {
            throw new Exception("Model {$class} does not exist");
        }

        if (!class_exists($relation)) {
            throw new Exception("Relation {$relation} does not exist");
        }

        $classRelation = new $relation;
        if (!$classRelation instanceof RelationshipInterface) {
            throw new Exception("Class {$relation} is not type of RelationshipInterface");
        }

        return $classRelation->createWith(
            $this,
            $class,
            $property
        );
    }

    public function makeRelationsWith(...$relations)
    {
        $relationsCreated = [];
        foreach ($relations as $relationArray) {
            if (count($relationArray) !== 3) {
                throw new Exception('To make relations, yout need to give exactly 3 parameters to relations methods');
            }
            [$class,$relation,$property] = $relationArray;

            $relationsCreated[] = $this->relation($class, $relation, $property);
        }

        if (count($relationsCreated) == 1) {
            return $relationsCreated[0]->items;
        }

        return $this->makeManyRelationsWith(...$relationsCreated);
    }


    private function makeManyRelationsWith(...$relations)
    {
        $relation1 = $relations[0];
        unset($relations[0]);

        foreach ($relations as $value) {
            $withName = $value->withName;
            foreach ($value->items as $key => $object) {
                if (!property_exists($relation1->items[$key], $withName)) {
                    $relation1->items[$key]->$withName = $object->$withName;
                }
            }
        }

        return $relation1->items;
    }

    // public function belongsTo(string $model, ?string $property = null)
    // {
    //     return RelationshipBelongsTo::createWith(
    //         static::class,
    //         $model,
    //         $property
    //     );
    // }

    public function relatedWith(array $ids, string $field = 'id')
    {
        $connection = Connection::getConnection();
        $query = "select * from {$this->table} where {$field} in (" . implode(',', $ids) . ')';
        $stmt = $connection->query($query);

        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
    }
}
