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
use Doctrine\ORM\AbstractQuery;

/**
 * Efficient iterator for Doctrine ORM collections.
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
class Iterator implements IteratorInterface
{

    /**
     * Hydration mode for query results.
     * 
     * @var int
     */
    private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

    /**
     * Name of the iterateBy field.
     * 
     * @var string
     */
    private $iterateBy;

    /**
     * Last value of the iterateBy.
     * 
     * @var mixed
     */
    private $lastValue = null;

    /**
     * Original query in a QueryBuilder form.
     * 
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $originalQb;

    /**
     * Current page number.
     * 
     * @var int Integral number greater than or equal to zero.
     */
    private $currentPage = 0;

    /**
     * Size of a single result page.
     * 
     * @var int Integral number greater than zero.
     */
    private $pageSize = 10;

    /**
     * Closure that retrieves value of the iterateBy field from a single result.
     * 
     * @var \Closure
     */
    private $pullClosure;

    /**
     * Modified copy of the original query.
     * 
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb = null;

    /**
     * {@inheritdoc}
     * 
     * @return array Array of hydrated elements. Empty if reached the end of collection or collection is empty.
     * 
     * @throws \UnexpectedValueException Thrown if iterator incorrectly initialized.
     */
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

        if ($this->currentPage++ > 0) {
            $qb->andWhere($this->qb->expr()->gt($this->iterateBy, $this->lastValue));
        }

        $result = $qb->getQuery()->getResult($this->hydrationMode);

        if (\count($result)) {
            $method = $this->pullClosure;
            $this->lastValue = $method($result[\count($result) - 1]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->qb = null;
        $this->currentPage = 0;
    }

    /**
     * Sets the hydration mode.
     * 
     * @param int $hydrationMode AbstractQuery constant.
     */
    public function setHydrationMode($hydrationMode)
    {
        $this->hydrationMode = $hydrationMode;
    }

    /**
     * Sets the iterateBy value.
     *
     * @param string $iterateBy Name of the query field to iterate by, has to be a primary key.
     */
    public function setIterateBy($iterateBy)
    {
        $this->iterateBy = $iterateBy;
        $this->rewind();
    }

    /**
     * Sets the pull closure.
     * 
     * @param \Closure $pull Closure that retrieves value of the iterateBy field from a single result.
     */
    public function setPullClosure(\Closure $pull)
    {
        $this->pullClosure = $pull;
        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryBuilder(QueryBuilder $qb)
    {
        $this->originalQb = $qb;
        $this->rewind();
    }

    /**
     * Sets the page size.
     * 
     * @param type $pageSize Integral number greater than zero.
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        $this->rewind();
    }

}