<?php

namespace jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Entity;

use jackdpeterson\Doctrine\QueryBuilder\Opinion\Slim\Action\HalWrapper;

interface JSONSerializableWithHAL extends \JsonSerializable
{
    public function jsonSerialize(HalWrapper $hal = null);
}