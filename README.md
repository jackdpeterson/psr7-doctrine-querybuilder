PSR-7 Doctrine QueryBuilder (Forked from ZF-Doctrine-QueryBuilder)
==================================================================

This library provides query builder directives from array parameters. This library was designed
to apply filters from an HTTP request to give an API fluent filter and order-by dialects.


Philosophy
----------

This project is intended to be an explicit way of working with collections.

Key principles:

1. All filters will be explicitly defined in each collection.
2. All order-by statements will be explicitly defined in each collection.
3. All dependencies (filters, order-by's) are to be injected in and fully defined (again, no magic).

Where this project has extremely opinionated examples you can find them in the Opinion\* 
namespace and are merely intended to guide you along when working up your own API.


Installation
------------

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```bash
$ composer require jackdpeterson/psr7-doctrine-querybuilder
```

ContainerInterop Configuration
______________________________


#Filter Manager configuration
```php
// Supporting Entity Manager (Filtering and Ordering of Collections)
    $c[\jackdpeterson\Doctrine\QueryBuilder\Filter\Service\FilterManager::class] = function ($c
    ): Filter\Service\FilterManager {
        return new Filter\Service\FilterManager([
            'MY_CUSTOM_FILTER' => \MyCompany\Filter\AFilterThatTheDIContainerCanFind::class,
            'eq' => Filter\ORM\Equals::class,
            'neq' => Filter\ORM\NotEquals::class,
            'lt' => Filter\ORM\LessThan::class,
            'lte' => Filter\ORM\LessThanOrEquals::class,
            'gt' => Filter\ORM\GreaterThan::class,
            'gte' => Filter\ORM\GreaterThanOrEquals::class,
            'isnull' => Filter\ORM\IsNull::class,
            'isnotnull' => Filter\ORM\IsNotNull::class,
            'in' => Filter\ORM\In::class,
            'notin' => Filter\ORM\NotIn::class,
            'between' => Filter\ORM\Between::class,
            'like' => Filter\ORM\Like::class,
            'notlike' => Filter\ORM\NotLike::class,
            'ismemberof' => Filter\ORM\IsMemberOf::class,
            'orx' => Filter\ORM\OrX::class,
            'andx' => Filter\ORM\AndX::class
        ]);
    };
```

#Order By Manager configuration


```php
$c[OrderBy\Service\OrderByManager::class] = function (): OrderBy\Service\OrderByManager {
        return new OrderBy\Service\OrderByManager(['field' => OrderBy\ORM\Field::class]);
    };
```


#The Query Builder 
(Factory that translates key/value querystring arguments to the right place)

```php
$c[QueryParametersBuilder::class] = function ($c) {
        return new QueryParametersBuilder();
    };

```


#A Collection class 
(before HAL decoration and so on ... returns instance of Paginator (Doctrine)) -- extends Collection\BaseCollection
```php
$c[MyCollection::class] = function ($c) {
        return new MyCollection(
            $c[\Doctrine\ORM\EntityManagerInterface::class],
            $c[\jackdpeterson\Doctrine\QueryBuilder\Filter\Service\FilterManager::class],
            $c[\jackdpeterson\Doctrine\QueryBuilder\OrderBy\Service\OrderByManager::class],
            $c[QueryParametersBuilder::class]
        );
    };
```


# REST Stuff


The Action class (highly opinionated, use your own!)


```php
$c[Action\Shop\GetAllMyCollectionAction::class] = function ($c) {
        return new Action\Shop\GetAllMyCollectionAction(
            $c[MyCollection::class],
            $c[Opinion\Slim\Action\HalWrapper::class]
        );
    };
```


## Opinionated things:
See src\Opinion\Slim\* for some practical examples.
