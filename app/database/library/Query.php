<?php

namespace app\database\library;

use app\database\model\Model;

class Query
{
    protected array $data;
    public ?Model $modelInstance = null;
    public readonly Paginate $paginate;

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

    public function crateQuery(bool $transform = true)
    {
        $select = $this->transform('select', $transform);
        $where = $this->transform('where', $transform);
        $order = $this->transform('order', $transform);
        $limit = $this->transform('limit', $transform);
        $offset = $this->transform('offset', $transform);
        $binds = $this->transform('binds', $transform);

        return [
            $select,
            $where,
            $order,
            $limit,
            $offset,
            $binds,
        ];
    }


    private function transform(string $field, bool $transform = true)
    {
        switch ($field) {
            case 'select':
                if (isset($this->data[$field]) && is_array($this->data[$field])) {
                    $this->data[$field] = rtrim(implode(',', $this->data[$field]));
                }

                break;
            case 'where':
                if (isset($this->data[$field]) && is_array($this->data[$field])) {
                    $this->data[$field] = ' where ' . implode(' ', $this->data[$field]);
                }

                break;
            case 'limit':
                if (isset($this->data[$field]) && !$transform) {
                    $this->data[$field] = ' limit ' . $this->data[$field];
                }

                break;
            case 'offset':
                if (isset($this->data[$field]) && !$transform) {
                    $this->data[$field] = ' offset ' . $this->data[$field];
                }

                break;
            case 'order':
                if (isset($this->data[$field]) && !$transform) {
                    $this->data[$field] = ' order by ' . $this->data[$field];
                }

                break;

            case 'binds':
                if (!isset($this->data[$field]) && !$transform) {
                    $this->data[$field] = [];
                }

                break;
        }

        return $this->get($field);
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
