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


use DSQ\Lucene\TreeExpression;

class SpanExpression extends TreeExpression
{
    const OP_AND = 'AND';
    const OP_OR = 'OR';
    const OP_NOT = 'NOT';

    /**
     * @param string $value
     * @param array $expressions
     * @param float $boost
     */
    public function __construct($value = self::OP_AND, array $expressions = array(), $boost = 1.0)
    {
        parent::__construct($value, $expressions, $boost); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $that = $this;
        $operator = $this->getValue();
        $expressionsStrings = array_map(function($expression) use ($that, $operator) {
            $expressionStr = $that->escape($expression);
            if (!$expression->hasPrecedence($that))
                $expressionStr = "($expressionStr)";
            return $expressionStr;
        }, $this->getExpressions());

        $result = implode(" $operator ", $expressionsStrings) ?: $this->noChildrenQuery();

        if ($this->getBoost() != 1.0)
            $result = "($result)";

        return $result . $this->boostSuffix();
    }

    /**
     * The lucene statement to use when there are no children.
     * It's like operations on a family of sets: the intersection of the empty family
     * is the universe set, the union of the empty family is the empty set.
     *
     * @return string
     */
    private function noChildrenQuery()
    {
        if ($this->getValue() == 'AND')
            return LuceneQuery::ALLQUERY;

        return LuceneQuery::EMPTYQUERY;
    }
}