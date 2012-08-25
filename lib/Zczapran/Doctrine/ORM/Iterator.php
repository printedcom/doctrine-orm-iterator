<?php

namespace Zczapran\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

/**
 * Description of Iterator
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
class Iterator implements IteratorInterface
{
    /**
     * @var int
     */
    private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;
    
    /**
     * @var string
     */
    private $iterateBy;
    
    /**
     * @var mixed
     */
    private $lastValue = null;
    
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $originalQb;
    
    /**
     * @var int
     */
    private $page = 0;
    
    /**
     * @var int
     */
    private $pageSize = 10;
    
    /**
     * @var \Closure
     */
    private $pullClosure;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb = null;
    
    public function next()
    {
        if (\is_null($this->qb)) {
            if (null === $this->originalQb) {
                throw new \UnexpectedValueException("Query Builder object not specified.");
            }
            
            if (null === $this->iterateBy) {
                throw new \UnexpectedValueException("IterateBy value not specified.");
            }
            
            $this->qb = clone $this->originalQb;
            $this->qb->orderBy($this->iterateBy);
            $this->qb->setMaxResults($this->pageSize);
        }
        
        $qb = clone $this->qb;
        
        if ($this->page++ > 0) {
            $qb->andWhere($this->qb->expr()->gt($this->iterateBy, $this->lastValue));
        }
        
        $result = $qb->getQuery()->getResult($this->hydrationMode);
        
        if (\count($result)) {
            $method = $this->pullClosure;
            $this->lastValue = $method($result[\count($result) - 1]);
        }
        
        return $result;
    }
    
    public function rewind()
    {
        $this->qb = null;
        $this->page = 0;
    }
    
    public function setHydrationMode($hydrationMode)
    {
        $this->hydrationMode = $hydrationMode;
    }
    
    public function setIterateBy($iterateBy)
    {
        $this->iterateBy = $iterateBy;
        $this->rewind();
    }
    
    public function setPullClosure(\Closure $pull)
    {
        $this->pullClosure = $pull;
        $this->rewind();
    }

    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->originalQb = $qb;
        $this->rewind();
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        $this->rewind();
    }

}