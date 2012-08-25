<?php

namespace Zczapran\Tests\Doctrine\ORM;

use Zczapran\Doctrine\ORM\Iterator;

/**
 * Description of IteratorTest
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
class IteratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * @var \Zczapran\Doctrine\ORM\Iterator
     */
    private $object;

    protected function setUp()
    {
        $this->object = new Iterator();
        $this->em = $this->getMockBuilder('Doctrine\\ORM\\EntityManager')
                ->setMethods(array('createQuery'))
                ->disableOriginalConstructor()
                ->getMock();
    }

    public function testNextNoQueryBuilderSpecified()
    {
        $this->setExpectedException('\UnexpectedValueException');
        $this->object->next();
    }

    public function testNextNoIterateBySpecified()
    {
        $qb = new \Doctrine\ORM\QueryBuilder($this->em);

        $this->setExpectedException('\UnexpectedValueException');
        $this->object->setQueryBuilder($qb);
        $this->object->next();
    }

    public function testNext()
    {
        $expectedResults = array(array(1), array(5), array(7));

        $query = new MockQuery();
        $query->setNextResult($expectedResults);

        $createQuery = function($dql) use ($query) {
                    $query->setDQL($dql);
                    return $query;
                };

        $this->em->expects($this->any())->method('createQuery')->will($this->returnCallback($createQuery));

        $qb = new \Doctrine\ORM\QueryBuilder($this->em);
        $qb->select('a.id, a.name')
                ->from('A', 'a')
                ->where('a.name = :name')->setParameter('name', 'abc');

        // initialize Iterator
        $this->object->setQueryBuilder($qb);
        $this->object->setIterateBy('a.id');
        $this->object->setPageSize(3);
        $this->object->setPullClosure(function($arr) {
                    return $arr[0];
                });

        // initial results
        $this->assertEquals($expectedResults, $this->object->next());
        $this->assertEquals('SELECT a.id, a.name FROM A a WHERE a.name = :name ORDER BY a.id ASC', $query->getDQL());
        $this->assertEquals(3, $query->getLastMaxResults());
        $this->assertEquals(1, \count($query->getLastParameters()));

        // second results
        $query->setNextResult(array());
        $this->assertEquals(array(), $this->object->next());
        $this->assertEquals('SELECT a.id, a.name FROM A a WHERE a.name = :name AND a.id > 7 ORDER BY a.id ASC', $query->getDQL());
        $this->assertEquals(3, $query->getLastMaxResults());
        $this->assertEquals(1, \count($query->getLastParameters()));
    }

}