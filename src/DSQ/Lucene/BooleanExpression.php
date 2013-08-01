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


class BooleanExpression extends BasicLuceneExpression
{
    const SHOULD = "";
    const MUST = "+";
    const MUST_NOT = "-";

    /**
     * @var LuceneExpression[]
     */
    private $expressions = array();

    public function __construct($operator = self::SHOULD, array $expressions = array(), $boost = 1.0)
    {
        parent::__construct($operator, $boost, $operator);

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
        if (!$expression instanceof LuceneExpression)
            $expression = new BasicLuceneExpression($expression);

        $this->expressions[] = $expression;

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
    public function __toString()
    {
        $that = $this;
        $operator = $this->getValue();
        $expressionsStrings = array_map(function($expression) use ($that, $operator) {
            $expressionStr = (string) $that->escape($expression);

            if ($expression instanceof BooleanExpression && $expression->numOfExpressions() > 1 && $expression->getBoost() == 1.0)
                $expressionStr = "($expressionStr)";

            return $operator . $expressionStr;
        }, $this->getExpressions());

        $result = implode(' ', $expressionsStrings);

        if ($this->getBoost() != 1.0)
            $result = "($result)";

        return $result . $this->boostSuffix();
    }
}