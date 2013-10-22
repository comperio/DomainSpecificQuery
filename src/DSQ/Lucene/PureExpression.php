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

/**
 * Class PureExpression
 * @package DSQ\Lucene
 */
class PureExpression extends AbstractLuceneExpression
{
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return false;
    }
} 