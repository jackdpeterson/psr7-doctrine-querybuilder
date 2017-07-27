<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use jackdpeterson\Doctrine\QueryBuilder\Collection\BaseCollection;

class BaseCollectionAction
{
    use AuthTrait;

    const COLLECTION_NAME = 'items';

    protected $hal;
    protected $mainQuery;

    public function __construct(BaseCollection $collectionQuery, HalWrapper $hal)
    {
        $this->mainQuery = $collectionQuery;
        $this->hal = $hal;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        return $this->hal->wrapCollection(static::COLLECTION_NAME, $request, $response,
            $this->mainQuery->fetchAll($request->getQueryParams(), $this->getUser($request)));
    }
}
