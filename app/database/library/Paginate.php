<?php

namespace app\database\library;

use app\database\model\Model;
use Exception;

class Paginate
{
    private int $actualPage;
    private int $pages;

    public function __construct(private Model $model, private Query $query)
    {
        $this->actualPage = $_GET['page'] ?? 1;
        $perPage = $this->getLimit();
        $totalRecords = $this->totalRecords();
        $this->query->offset(ceil($this->actualPage - 1) * $perPage);
        $this->pages = ceil($totalRecords / $perPage);
    }

    private function getLimit()
    {
        $limit = $this->query->get('limit');

        if (!$limit) {
            throw new Exception('To paginate please use limit method');
        }

        return $limit;
    }

    private function totalRecords()
    {
        return $this->model->count($this->query)->total;
    }

    public function createLinks(int $linksPerPage = 5)
    {
        $startLink = max(1, $this->actualPage - floor($linksPerPage / 2));
        $endLink = min($startLink + $linksPerPage - 1, $this->pages);
        for ($i = $startLink; $i <= $endLink; $i++) {
            if ($i == $this->actualPage) {
                echo "<strong>$i</strong> ";
            } else {
                echo "<a href='?page=$i'>$i</a> ";
            }
        }
    }
}
