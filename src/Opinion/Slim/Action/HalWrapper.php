<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Action;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;
use Slim\Route;
use jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Entity\JSONSerializableWithHAL;

class HalWrapper
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function wrapCollection(string $collectionName, Request $req, Response $res, Paginator $pager): Response
    {
        for ($items = [], $results = $pager->getIterator(); $results->valid(); $results->next()) {
            $items[] = $this->decorate($results->current());
        }

        /** @var Route $currentRoute */
        $currentRoute = $req->getAttribute('route');
        if (!($name = $currentRoute->getName())) {
            throw new \LogicException("Unnamed route used in HAL context");
        }

        $currentPath = $this->router->pathFor($currentRoute->getName(), $currentRoute->getArguments());
        $currentQs = $req->getUri()->getQuery();

        return $res->withJson([
            '_links' => [
                'self' => ['href' => $currentPath . ($currentQs ? ('?' . $currentQs) : '')],
            ],
            'count' => $pager->count(),
            '_embedded' => [$collectionName => $items]
        ]);
    }

    protected function decorate(\JsonSerializable $item): array
    {
        if ($item instanceof JSONSerializableWithHAL) {
            return $item->jsonSerialize($this);
        }

        return $item->jsonSerialize();
    }

    public function getLinkSkeleton($class, $id, array $params = [])
    {
        $params['id'] = $id;

        return [
            '_links' => [
                'self' => [
                    'href' => $this->router->pathFor($class, $params)
                ]
            ]
        ];
    }
}
