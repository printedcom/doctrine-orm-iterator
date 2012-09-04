# Doctrine ORM Iterator #

[![Build Status](https://secure.travis-ci.org/zczapran/doctrine-orm-iterator.png)](http://travis-ci.org/zczapran/doctrine-orm-iterator)

## Introduction ##

Efficient iterator for Doctrine ORM. Allows for paginated traversing of database collection by specifing a QueryBuilder object that represents a query that retrieve the desired data. The only requirement is that the collection has to have a key with ability to order by it.

Each next query instead of using limit-offset query type is using the last item's key value to retrieve next resultset with keys greater then previous.

## Example Usage ##

### SimpleIterator ###
In SimpleIterator the hydration mode is always HYDRATE_OBJECT and the root entity needs to have 'id' field as its primary key.
```php
$qb = $em->createQueryBuilder()->select('u')->from('User', 'u');
$iterator = new SimpleIterator($qb);
while ($result = $iterator->next()) {
  foreach ($result as $item) {
    // operate on single result items
  }
  $em->clear(); // here you can safely clear the entity manager
}
```
  
### Iterator ###
Iterator provides more configuration abilities then SimpleIterator. It allows to specify hydration mode and a function
used to retrieve value of the key (useful for non-standard keys and/or hydration modes other than HYDRATE_OBJECT).
```php
$qb = $em->createQueryBuilder()->select('u')->from('User', 'u');
$pullClosure = function($entity) { return $entity->getId(); };

$iterator = new Iterator();
$iterator->setQueryBuilder($qb);
$iterator->setIterateBy('u.id');
$iterator->setPullClosure($pullClosure);

while ($result = $iterator->next()) {
  foreach ($result as $item) {
    // operate on single result items
  }
  $em->clear(); // here you can safely clear the entity manager
}
```