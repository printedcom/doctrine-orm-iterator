<?php

namespace Zczapran\Tests\Doctrine\ORM;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * This is mocked query class. Unfortunately DoctrineORM doesn't provide an interface for queries.
 */
class MockQuery
{

    private $dql;
    private $lastFirstResult;
    private $lastMaxResults;
    private $lastParameters;
    private $nextResult = array();

    public function getDQL()
    {
        return $this->dql;
    }

    public function getLastFirstResult()
    {
        return $this->lastFirstResult;
    }

    public function getLastMaxResults()
    {
        return $this->lastMaxResults;
    }

    public function getLastParameters()
    {
        return $this->lastParameters;
    }

    public function getResult()
    {
        $result = $this->nextResult;
        $this->nextResult = array();
        return $result;
    }

    public function setDQL($dql)
    {
        return $this->dql = $dql;
    }

    public function setFirstResult($firstResult)
    {
        $this->lastFirstResult = $firstResult;
        return $this;
    }

    public function setMaxResults($maxResults)
    {
        $this->lastMaxResults = $maxResults;
        return $this;
    }

    public function setNextResult(array $result)
    {
        $this->nextResult = $result;
    }

    public function setParameters(ArrayCollection $parameters)
    {
        $this->lastParameters = $parameters;
        return $this;
    }

}