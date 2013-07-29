<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression\Builder;

use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;

class Builder
{
    /**
     * @var Expression[]
     */
    private $stack = array();

    /**
     * Build a basic expression
     *
     * @param mixed $value
     *
     * @return $this The current instance
     */
    public function value($value)
    {
        $expression = new BasicExpression($value);

        if (!$this->isStackEmpty()) {
            $this->addChild($expression);
        } else {
            $this->push($expression);
        }

        return $this;;
    }
    /**
     * Build a field expression (i.e. a binary expression)
     *
     * @param mixed $name The name of the field
     * @param mixed $value The value of the field
     * @param string $operator
     *
     * @return $this The current instance
     *
     * @throws ExpressionTypeException
     */
    public function field($name, $value, $operator = '=')
    {
        $field = new BinaryExpression($operator, $name, $value);

        if (!$this->isStackEmpty()) {
            $this->addChild($field);
        } else {
            $this->push($field);
        }

        return $this;
    }

    /**
     * Build a tree expression
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function tree($value)
    {
        $tree = new TreeExpression($value);

        if (!$this->isStackEmpty())
            $this->addChild($tree);

        $this->push($tree);

        return $this;
    }

    /**
     * End the construction of a Tree Expression
     *
     * @return $this The current instance
     */
    public function end()
    {
        $this->pop();

        return $this;
    }

    /**
     * @return Expression
     */
    public function getExpression()
    {
        return $this->currentExpression();
    }

    /**
     * @return bool
     */
    private function isStackEmpty()
    {
        return !(bool) $this->stack;
    }

    /**
     * Push an Expression on the top of the stack
     *
     * @param Expression $expression
     *
     * @return $this
     */
    private function push(Expression $expression)
    {
        $this->stack[] = $expression;

        return $this;
    }

    /**
     * Pop the last element from the stack
     *
     * @return Expression
     *
     * @throws EmptyStackException
     */
    private function pop()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('Builder stack is empty, cannot popping');

        return array_pop($this->stack);
    }

    /**
     * @return Expression
     *
     * @throws EmptyStackException
     */
    private function currentExpression()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('Builder stack is empty');

        return $this->stack[count($this->stack) - 1];
    }

    /**
     * Add an expression as a child of the current expression
     *
     * @param Expression $expression
     *
     * @throws ExpressionTypeException
     *
     * @return $this The current instance
     */
    private function addChild(Expression $expression)
    {
        $currentExp = $this->currentExpression();

        if (!$currentExp instanceof TreeExpression)
            throw new ExpressionTypeException('Could not add expressions to a non-tree expression');

        $currentExp->addChild($expression);

        return $this;
    }
} 