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
        return $this->binary($operator, $name, $value);
    }

    /**
     * @param $operator
     * @param null $left
     * @param null $right
     * @return $this
     */
    public function binary($operator, $left = null, $right = null)
    {
        $binary = new BinaryExpression($operator, $left, $right);

        if (!$this->isStackEmpty()) {
            $this->addChild($binary);
            if (!isset($right))
                $this->push($binary);
        } else {
            $this->push($binary);
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
     * Return the current expression and empty the stack
     *
     * @throws EmptyStackException
     *
     * @return Expression
     */
    public function getExpression()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('The stack of the builder is empty');

        $expression =  $this->stack[0];

        $this->stack = array();

        return $expression;
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

        if ($currentExp instanceof TreeExpression) {
            $currentExp->addChild($expression);
        } elseif ($currentExp instanceof BinaryExpression) {
            if ($currentExp->getLeft()->getValue() === null)
                $currentExp->setLeft($expression);
            else
                $currentExp->setRight($expression);
        } else {
            throw new ExpressionTypeException('Could not add child expression to the current expression');
        }

        return $this;
    }
} 