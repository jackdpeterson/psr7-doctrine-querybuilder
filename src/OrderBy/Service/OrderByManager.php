<?php

namespace jackdpeterson\Doctrine\QueryBuilder\OrderBy\Service;

use Doctrine\ORM\QueryBuilder;
use RuntimeException;
use jackdpeterson\Doctrine\QueryBuilder\OrderBy\OrderByInterface;

class OrderByManager
{

    protected $orderByHandlers;


    public function __construct(array $orderByHandlers)
    {
        $this->orderByHandlers = $orderByHandlers;
    }


    /**
     * @param string $orderByHandlerName
     * @return OrderByInterface
     */
    protected function get(string $orderByHandlerName): OrderByInterface
    {
        if (!array_key_exists($orderByHandlerName, $this->orderBy)) {
            throw new \RuntimeException("QB Order By Handler Not found: " . $orderByHandlerName);
        }

        if (! class_exists($this->orderByHandlers[$orderByHandlerName])) {
            throw new \RuntimeException("QB Order By Handler Class Not found in autoloader: " . $orderByHandlerName);
        }

        $orderByHandler = new $this->orderByHandlers[$orderByHandlerName]();

        if (!$orderByHandler instanceof OrderByInterface ) {
            throw new \RuntimeException(sprintf('Requested %1$s however, %1$s is not an instance of OrderByInterface!',
                $orderByHandlerName));
        }

        return $this->orderByHandlers[$orderByHandlerName];
    }

    /**
     * @var string
     */
    protected $instanceOf = OrderByInterface::class;

    public function orderBy(QueryBuilder $queryBuilder, $metadata, $orderBy)
    {
        foreach ($orderBy as $option) {
            if (empty($option['type'])) {
                throw new RuntimeException('Array element "type" is required for all orderby directives');
            }

            $orderByHandler = $this->get(strtolower($option['type']), [$this]);
            $orderByHandler->orderBy($queryBuilder, $metadata, $option);
        }
    }

}
