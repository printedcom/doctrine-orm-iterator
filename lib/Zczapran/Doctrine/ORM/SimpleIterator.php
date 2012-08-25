<?php

namespace Zczapran\Doctrine\ORM;

/**
 * Description of SimpleIterator
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
class SimpleIterator implements IteratorInterface
{

    /**
     * @var Iterator
     */
    private $iterator;

    public function __construct()
    {
        $this->iterator = new Iterator();
        $pullClosure = function($entity) {
                    return $entity->getId();
                };
        $this->iterator->setPullClosure($pullClosure);
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function rewind()
    {
        $this->iterator->rewind();
    }

    public function setQueryBuilder(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->iterator->setQueryBuilder($qb);
        $this->iterator->setIterateBy($qb->getRootAlias() . '.id');
    }

}