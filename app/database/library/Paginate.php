<?php

namespace app\database\library;

use app\database\model\Model;
use Exception;

class Paginate
{
    protected int $actualPage;
    protected int $pages;
    protected int $totalRecords;

    public function __construct(protected Model $modelInstance, Query $query)
    {
        $this->actualPage = $_GET['page'] ?? 1;
        $perPage = $this->getLimit($query);
        $query->offset(ceil($this->actualPage - 1) * $perPage);
        $this->totalRecords = $this->totalRecords($query);
        $this->pages = ceil($this->totalRecords / $perPage);
    }

    private function getLimit(Query $query)
    {
        $perPage = $query->get('limit');
        if (!$perPage) {
            throw new Exception('To paginate you need use limit method');
        }

        return $perPage;
    }

    private function totalRecords(Query $query)
    {
        return $this->modelInstance->count($query)->total;
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
