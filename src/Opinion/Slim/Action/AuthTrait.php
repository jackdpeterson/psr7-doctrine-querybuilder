<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Action;

use jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Authorization\UserInterface;
use jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Exception\AuthException;
use Psr\Http\Message\ServerRequestInterface;

trait AuthTrait
{
    public function getUser(ServerRequestInterface $request): UserInterface
    {
        $user = $request->getAttribute('user');
        if (!($user instanceof UserInterface)) {
            throw new AuthException('No valid user was associated with this HTTP request.');
        }

        return $user;
    }
}
