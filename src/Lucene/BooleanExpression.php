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


class BooleanExpression extends TreeExpression
{
    const SHOULD = "";
    const MUST = "+";
    const MUST_NOT = "-";

    /**
     * @param string $operator
     * @param array $expressions
     * @param float $boost
     */
    public function __construct($operator = self::SHOULD, array $expressions = array(), $boost = 1.0)
    {
        parent::__construct($operator, $expressions, $boost);
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

            if (!$expression->hasPrecedence($that))
                $expressionStr = "($expressionStr)";

            return $operator . $expressionStr;
        }, $this->getExpressions());

        $result = implode(' ', $expressionsStrings);

        if ($this->getBoost() != 1.0)
            $result = "($result)";

        return $result . $this->boostSuffix();
    }
}