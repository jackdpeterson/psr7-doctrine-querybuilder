<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Filter\Service;

use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use jackdpeterson\Doctrine\QueryBuilder\Filter\FilterInterface;

class FilterManager
{

    protected $filters;


    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @param string $filterName
     * @return FilterInterface
     */
    protected function get(string $filterName): FilterInterface
    {
        if (!array_key_exists($filterName, $this->filters)) {
            throw new \RuntimeException("QB Filter Not found: " . $filterName);
        }
        if (!$this->filters[$filterName] instanceof FilterInterface) {
            throw new \RuntimeException(sprintf('Requested %1$s however, %1$s is not an instance of FilterInterface!',
                $filterName));
        }

        return $this->filters[$filterName];
    }


    /**
     * @var string
     */
    protected $instanceOf = FilterInterface::class;

    public function filter(QueryBuilder $queryBuilder, $metadata, $filters)
    {
        foreach ($filters as $option) {
            if (empty($option['type'])) {
                throw new RuntimeException('Array element "type" is required for all filters');
            }

            $filter = $this->get(strtolower($option['type']), [$this]);
            $filter->filter($queryBuilder, $metadata, $option);
        }
    }
}
