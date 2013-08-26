<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression\Builder;;

use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\UnaryExpression;

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
     * @param string $type
     *
     * @return $this The current instance
     */
    public function value($value, $type = 'basic')
    {
        $expression = new BasicExpression($value, $type);

        if (!$this->isStackEmpty()) {
            $this->addChild($expression);
        } else {
            $this->push($expression);
        }

        return $this;
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
    public function field($name, $value = null, $operator = '=')
    {
        return $this->binary($operator, $name, $value, $name);
    }

    /**
     * @param $operator
     * @param null $left
     * @param null $right
     * @param null $type
     * @return $this
     */
    public function binary($operator, $left = null, $right = null, $type = null)
    {
        $binary = new BinaryExpression($operator, $left, $right, $type);

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
     * @param $operator
     *
     * @param null $child
     *
     * @return $this The current instance
     */
    public function unary($operator, $child = null)
    {
        $unary = new UnaryExpression($operator, $child);

        if (!$this->isStackEmpty()) {
            $this->addChild($unary);
            if (!isset($child))
                $this->push($unary);
        } else {
            $this->push($unary);
        }

        return $this;
    }

    /**
     * Build a tree expression
     *
     * @param mixed $value
     * @param $arg1,... an optional list of values
     *
     * @return $this
     */
    public function tree($value /*, $arg1, $arg2..*/)
    {
        $children = func_get_args();
        array_shift($children);

        $tree = new TreeExpression($value);

        if (!$this->isStackEmpty())
            $this->addChild($tree);

        foreach ($children as $arg) {
            $tree->addChild($arg);
        }

        if (!$children)
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
     * Magic method to create tree nodes
     *
     * @param $name
     * @param array $args
     *
     * @return $this
     */
    public function __call($name, $args)
    {
        array_unshift($args, $name);

        return call_user_func_array(array($this, 'tree'), $args);
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
        } elseif ($currentExp instanceof UnaryExpression) {
            $currentExp->setChild($expression);
        } else {
            throw new ExpressionTypeException('Could not add child expression to the current expression');
        }

        return $this;
    }
} 