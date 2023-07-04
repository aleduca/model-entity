<?php

namespace app\database\library;

use app\database\model\Model;

class Query
{
    protected array $data;
    public ?Model $modelInstance = null;
    public Paginate $paginate;

    public function select(array|string $select)
    {
        $this->data['select'] = $select;

        return $this;
    }

    public function where(string $field, string $operator, string|int $value, ?string $logic = null)
    {
        $this->data['where'][] = "{$field} {$operator} :{$field} {$logic}";
        $this->data['binds'][$field] = $value;

        return $this;
    }

    public function limit(int $limit)
    {
        $this->data['limit'] = $limit;

        return $this;
    }

    public function order(string $order)
    {
        $this->data['order'] = $order;

        return $this;
    }

    public function model(string $model)
    {
        if (class_exists($model) && !$this->modelInstance) {
            $this->modelInstance = new $model;
        }

        return $this;
    }

    public function offset(int $offset)
    {
        $this->data['offset'] = $offset;

        return $this;
    }

    public function paginate(string $model)
    {
        if (class_exists($model) && !$this->modelInstance) {
            $this->modelInstance = new $model;
            $this->paginate = new Paginate($this->modelInstance, $this);
        }

        return $this;
    }

    public function crateQuery(array $transformsSelected)
    {
        $transformed = [];
        foreach ($transformsSelected as $transform) {
            $transformed[$transform] = $this->transform($transform);
        }

        return array_values($transformed);
    }


    private function transform(string $field)
    {
        $data = [];
        switch ($field) {
            case 'select':
                if (isset($this->data[$field]) && is_array($this->data[$field])) {
                    $data[$field] = rtrim(implode(',', $this->data[$field]));
                }

                break;
            case 'where':
                if (isset($this->data[$field]) && is_array($this->data[$field])) {
                    $data[$field] = ' where ' . implode(' ', $this->data[$field]);
                }

                break;
            case 'limit':
                if (isset($this->data[$field])) {
                    $data[$field] = ' limit ' . $this->data[$field];
                }

                break;
            case 'offset':
                if (isset($this->data[$field])) {
                    $data[$field] = ' offset ' . $this->data[$field];
                }

                break;
            case 'order':
                if (isset($this->data[$field])) {
                    $data[$field] = ' order by ' . $this->data[$field];
                }

                break;
        }

        return $data[$field] ?? null;
    }

    public function get(string $field)
    {
        return $this->data[$field] ?? null;
    }


    public function getData()
    {
        $this->transform('select');
        $this->transform('where');
        $this->transform('limit');
        $this->transform('order');

        return $this->data;
    }
}
