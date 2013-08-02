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


class RangeExpression extends AbstractLuceneExpression
{
    private $includeLeft = true;
    private $includeRight = true;

    /**
     * @param string $from
     * @param string $to
     * @param float $boost
     * @param bool $includeLeft
     * @param bool $includeRight
     */
    public function __construct($from = '*', $to = '*', $boost = 1.0, $includeLeft = true, $includeRight = true)
    {
        parent::__construct(array('from' => $from, 'to' => $to) , $boost, 'range');

        $this->includeLeft = $includeLeft;
        $this->includeRight = $includeRight;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $value = $this->getValue();

        $result = $this->includeLeft ? '[' : '{';
        $result .= "{$this->escape($value['from'])} TO {$this->escape($value['to'])}";
        $result .= $this->includeRight ? ']' : '}';
        $result .= $this->boostSuffix();

        return $result;
    }

    /**
     * @param mixed $expression
     * @return bool
     */
    public function hasPrecedence($expression)
    {
        return true;
    }
}