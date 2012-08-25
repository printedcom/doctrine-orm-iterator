<?php

namespace Zczapran\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * Description of Iterator
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
interface IteratorInterface
{

    public function next();
    
    public function rewind();

    public function setQueryBuilder(QueryBuilder $qb);

}