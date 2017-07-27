<?php


namespace jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Authorization;


interface UserInterface
{
    /**
     * @abstract returns true if the instance of the user has a keyed identifier (e.g., user.id)
     * @return bool
     */
    public function assertHasIdentifier(): bool;
}