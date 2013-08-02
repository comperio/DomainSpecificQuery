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


abstract class TreeExpression extends AbstractLuceneExpression
{
    /**
     * @var LuceneExpression[]
     */
    private $expressions = array();

    /**
     * @param LuceneExpression|mixed $value
     * @param array $expressions
     * @param float $boost
     */
    public function __construct($value, array $expressions = array(), $boost = 1.0)
    {
        parent::__construct($value, $boost, $value);

        $this->setExpressions($expressions);
    }

    /**
     * Add subexpression to the expression
     *
     * @param LuceneExpression|string $expression   The sub-expression
     * @return $this                                The current instance
     */
    public function addExpression($expression)
    {
        $this->expressions[] = $this->expr($expression);

        return $this;
    }

    /**
     * Set the set of subexpressions
     *
     * @param LuceneExpression[] $expressions   The array of expressions
     * @return $this
     */
    public function setExpressions(array $expressions)
    {
        foreach ($expressions as $expression) {
            $this->addExpression($expression);
        }

        return $this;
    }

    /**
     * Get the array of subexpressions
     *
     * @return LuceneExpression[]
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @return int  The number of subexpressions
     */
    public function numOfExpressions()
    {
        return count($this->expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrecedence($expression)
    {
        return $this->numOfExpressions() <= 1 || $this->getBoost() != 1.0;
    }
} 