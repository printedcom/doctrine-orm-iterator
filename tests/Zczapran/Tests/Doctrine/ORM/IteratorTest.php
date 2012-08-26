<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software is licensed under the MIT license. 
 * For more information, see <http://doctrine-orm-iterator.zczapran.com>.
 */

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