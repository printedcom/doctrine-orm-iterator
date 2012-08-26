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