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

namespace Zczapran\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * Class that simplifies usage of Iterator.
 * 
 * Works only with trivial primary keys (have to be named 'id').
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
class SimpleIterator implements IteratorInterface
{

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * Constructor.
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb Traverse query in QueryBuilder form
     * @param int $pageSize Maximum number of results retrieved in each next() call
     */
    public function __construct(QueryBuilder $qb, $pageSize = 10)
    {
        $this->iterator = new Iterator();
        $pullClosure = function($entity) {
                    return $entity->getId();
                };
        $this->iterator->setPullClosure($pullClosure);
        $this->iterator->setPageSize($pageSize);
        $this->setQueryBuilder($qb);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * Sets the base query in QueryBuilder form.
     * 
     * Also sets the iterateBy to rootAlias.id.
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->iterator->setQueryBuilder($qb);
        $this->iterator->setIterateBy($qb->getRootAlias() . '.id');
    }

}