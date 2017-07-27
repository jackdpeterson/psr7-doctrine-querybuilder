<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Collection;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use jackdpeterson\Doctrine\QueryBuilder\Filter\Service\FilterManager;
use jackdpeterson\Doctrine\QueryBuilder\OrderBy\Service\OrderByManager;

abstract class BaseCollection
{
    const FILTER_CONFIGS = [];
    const ORDER_BY_CONFIGS = [];

    protected $entityManager;

    protected $filterManager;

    protected $orderByManager;

    protected $builder;

    public function __construct(
        EntityManagerInterface $entityManager,
        FilterManager $filterManager,
        OrderByManager $orderByManager,
        QueryParametersBuilder $builder
    ) {
        $this->entityManager = $entityManager;
        $this->filterManager = $filterManager;
        $this->orderByManager = $orderByManager;
        $this->builder = $builder;
    }

    public function fetchAll(array $params, $user = null): Paginator
    {
        $emQueryParams = $this->builder->fromArray($params, static::FILTER_CONFIGS, static::ORDER_BY_CONFIGS);

        $qb = $this->entityManager->createQueryBuilder()->select('row')->from($this->getEntityClass(), 'row');
        $qb = $this->decorate($qb, $user);

        $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($this->getEntityClass());

        $this->filterManager->filter($qb, $metadata, $emQueryParams->getFilters());
        $this->orderByManager->orderBy($qb, $metadata, $emQueryParams->getOrderByStatements());

        // pagination (which page and so on)
        $qb->setFirstResult($emQueryParams->calculateOffset());
        $qb->setMaxResults($emQueryParams->getPageSize());

        return new Paginator($qb, false);
    }

    abstract protected function decorate(QueryBuilder $qb, $user): QueryBuilder;

    abstract protected function getEntityClass(): string;
}
