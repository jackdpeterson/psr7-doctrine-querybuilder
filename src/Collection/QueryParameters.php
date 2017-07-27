<?php

declare(strict_types=1);

namespace jackdpeterson\Doctrine\QueryBuilder\Collection;

final class QueryParameters
{
    protected $pageNum;

    protected $pageSize;

    protected $filters;

    protected $orderByStatements;

    public function __construct(int $pageNum, int $pageSize, array $filters = [], array $orderByStatements = [])
    {
        $this->pageNum = $pageNum;
        $this->pageSize = $pageSize;
        $this->filters = $filters;
        $this->orderByStatements = $orderByStatements;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOrderByStatements(): array
    {
        return $this->orderByStatements;
    }


    public function getPageNum(): int
    {
        return $this->pageNum;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function calculateOffset(): int
    {
        return $this->pageNum * $this->pageSize;
    }
}
