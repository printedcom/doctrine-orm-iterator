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
 * Interface for Doctrine ORM collection iterators.
 *
 * @author Zbigniew Czapran <zczapran@gmail.com>
 */
interface IteratorInterface
{

    /**
     * Gets next part of the queried collection.
     * 
     * @return array Array of hydrated elements. Empty if reached the end of collection or collection is empty.
     * 
     * @throws \UnexpectedValueException Thrown if iterator incorrectly initialized.
     */
    public function next();
    
    /**
     * Sets the pointer to the beginning of the collection.
     */
    public function rewind();

    /**
     * Sets the query in a form of QueryBuilder object.
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb Query in a form of QueryBuilder object.
     */
    public function setQueryBuilder(QueryBuilder $qb);

}