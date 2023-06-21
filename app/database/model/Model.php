<?php

namespace app\database\model;

use app\database\Connection;
use app\database\entity\Entity;
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

    public function belongsTo(string $model, ?string $property = null)
    {
        return RelationshipBelongsTo::createWith(
            static::class,
            $model,
            $property
        );
    }

    public function relatedWith(array $ids)
    {
        $connection = Connection::getConnection();
        $query = "select * from {$this->table} where id in (" . implode(',', $ids) . ')';
        $stmt = $connection->query($query);

        return $stmt->fetchAll(PDO::FETCH_CLASS, $this->getEntity());
    }
}
