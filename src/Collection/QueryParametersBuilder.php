<?php

declare(strict_types=1);

namespace jackdpeterson\Doctrine\QueryBuilder\Collection;

final class QueryParametersBuilder
{
    protected $defaultPageSize;

    protected $minPageSize;

    protected $maxPageSize;

    protected $pageKey;

    protected $pageSizeKey;

    protected $orderByKey;

    public function __construct(
        int $defaultPageSize = 10,
        int $minPageSize = 10,
        int $maxPageSize = 1000,
        string $pageKey = 'page',
        string $pageSizeKey = 'page_size',
        string $orderByKey = 'order_by'
    ) {
        $this->defaultPageSize = $defaultPageSize;
        $this->minPageSize = min($minPageSize, $defaultPageSize);
        $this->maxPageSize = max($maxPageSize, $defaultPageSize);
        $this->pageKey = $pageKey;
        $this->pageSizeKey = $pageSizeKey;
        $this->orderByKey = $orderByKey;
    }

    public function fromArray(array $params, array $filterConfigs = [], array $orderConfigs = []): QueryParameters
    {
        $filters = [];
        foreach ($filterConfigs as $param => $filterConfig) {
            if (!isset($params[$param])) {
                $param = str_replace('.', '_', $param);
            }
            if (isset($params[$param])) { // if filter exists in both query and config, sub in value and apply filter
                $filterConfig['value'] = $params[$param];
                $filters[] = $filterConfig;
            }
        }

        $orderByStatements = array_filter(array_map(function ($clause) use ($orderConfigs) {
            $clauseParts = explode('.', $clause); // [field, direction]
            if (!isset($orderConfigs[$clauseParts[0]])) {
                return null; // order-by doesn't exist in config; omit
            }

            $config = $orderConfigs[$clauseParts[0]];
            if (isset($clauseParts[1]) && in_array($clauseParts[1], ['asc', 'desc'])) {
                $config['direction'] = $clauseParts[1]; // use default order if one isn't provided
            }

            return $config;
        }, explode(',', $params[$this->orderByKey] ?? ''))); // mapped this way to maintain user-specified sort order

        if (!count($orderByStatements) && isset($orderConfigs['default'])) {
            $orderByStatements[] = $orderConfigs['default'];
        }

        return new QueryParameters(
            is_numeric($pageNum = $params[$this->pageKey] ?? 1) && $pageNum >= 1 ? (int)$pageNum - 1 : 0,
            is_numeric($pageSize = $params[$this->pageSizeKey] ?? $this->defaultPageSize) &&
            $pageSize >= $this->minPageSize && $pageSize <= $this->maxPageSize ?
                (int)$pageSize : $this->defaultPageSize,
            array_values($filters),
            array_values($orderByStatements)
        );
    }
}
