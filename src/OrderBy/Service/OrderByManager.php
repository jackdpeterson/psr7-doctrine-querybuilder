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
        if (!array_key_exists($orderByHandlerName, $this->orderByHandlers)) {
            throw new \RuntimeException("QB Order By Handler Not found: " . $orderByHandlerName);
        }

        if (!class_exists($this->orderByHandlers[$orderByHandlerName])) {
            throw new \RuntimeException("QB Order By Handler Class Not found in autoloader: " . $orderByHandlerName);
        }

        if (!is_subclass_of($this->orderByHandlers[$orderByHandlerName], OrderByInterface::class, true)) {
            throw new \RuntimeException('Provided class name is not an OrderByInterface');
        }

        $orderByHandler = new $this->orderByHandlers[$orderByHandlerName]([$this]);

        if (!$orderByHandler instanceof OrderByInterface) {
            throw new \RuntimeException(sprintf('Requested %1$s however, %1$s is not an instance of OrderByInterface!',
                $orderByHandlerName));
        }

        return $this->orderByHandlers[$orderByHandlerName];
    }

    public function orderBy(QueryBuilder $queryBuilder, $metadata, $orderBy)
    {
        foreach ($orderBy as $option) {
            if (count($this->orderByHandlers) === 1) {
                $option['type'] = key($this->orderByHandlers);
            }

            if (empty($option['type'])) {
                throw new RuntimeException('Array element "type" is required for all orderby directives');
            }

            $orderByHandler = $this->get(strtolower($option['type']));
            $orderByHandler->orderBy($queryBuilder, $metadata, $option);
        }
    }
}
