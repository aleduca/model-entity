<?php

namespace app\database\model;

use app\database\Connection;
use app\database\entity\Entity;
use app\database\interfaces\RelationshipInterface;
use app\database\library\Helpers;
use app\database\library\Query;
use app\database\relations\RelationshipBelongsTo;
use Exception;
use PDO;

abstract class Model
{
    protected string $table;
    protected ?Query $query = null;

    private function getEntity()
    {
        $class = Helpers::getClassShortName(static::class);
        $entity = "app\\database\\entity\\{$class}Entity";

        if (!class_exists($entity)) {
            throw new Exception("Entity {$entity} does not exist");
        }

        return $entity;
    }

    public function all()
    {
        try {
            $connection = Connection::getConnection();
            [$select, $where,$order,$limit,$offset,$binds] = $this->query->crateQuery([
                'select', 'where', 'order', 'limit', 'offset', 'binds',
            ]);
            $select = $select ?? '*';
            $query = "select {$select} from {$this->table}{$where}{$order}{$limit}{$offset}";
            $prepare = $connection->prepare($query);
            $prepare->execute($binds);

            return $prepare->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }


    public function count(Query $query)
    {
        try {
            $connection = Connection::getConnection();
            [$where,$binds] = $query->crateQuery(['where', 'binds']);
            $query = "select count(*) as total from {$this->table}{$where}";
            $prepare = $connection->prepare($query);
            $prepare->execute($binds);

            return $prepare->fetchObject($this->getEntity());
        } catch (\PDOException $th) {
            var_dump($th->getMessage());
        }
    }

    public function execute(Query $query)
    {
        $this->query = $query;

        return $this;
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

    private function relation(string $class, string $relation, string $property, array $results)
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
            static::class,
            $class,
            $property,
            $results
        );
    }

    public function makeRelationsWith(...$relations)
    {
        $relationsCreated = [];
        $results = $this->all();
        foreach ($relations as $relationArray) {
            if (count($relationArray) !== 3) {
                throw new Exception('To make relations, yout need to give exactly 3 parameters to relations methods');
            }
            [$class,$relation,$property] = $relationArray;

            $relationsCreated[] = $this->relation($class, $relation, $property, $results);
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

        var_dump('related with executed');

        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
    }
}
