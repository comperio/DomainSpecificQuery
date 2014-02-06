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

class TermExpression extends AbstractLuceneExpression
{
    /**
     * @param mixed|LuceneExpression $value      The value of the expression
     * @param float $boost                       The boost factor
     * @param string $type                       The type of the expression
     */
    public function __construct($value, $boost = 1.0, $type = 'term')
    {
        parent::__construct($value, $boost, $type);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->escape($this->getValue()) . $this->boostSuffix();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return strstr($this->getValue(), ' ') === false;
    }
}