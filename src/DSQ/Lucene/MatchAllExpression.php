<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Lucene;


class MatchAllExpression extends BasicLuceneExpression
{
    /**
     * @param float $boost
     */
    public function __construct($boost = 1.0)
    {
        parent::__construct('*:*', $boost, 'matchall');
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return '*:*' . $this->boostSuffix();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return true;
    }
} 