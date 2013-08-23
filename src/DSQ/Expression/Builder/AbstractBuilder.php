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


use DSQ\Expression\Expression;

abstract class AbstractBuilder
{
    /** @var AbstractBuilder[] */
    private $builders = array();

    /** @var array AbstractBuilder[] */
    private $buildersStack = array();

    /** @var array Expression[] */
    private $stack = array();

    /**
     * @param $name
     * @param AbstractBuilder $builder
     *
     * @return $this
     */
    public function registerBuilder($name, AbstractBuilder $builder)
    {
        $builder
            ->setStack($this->stack)
            ->setBuilders($this->builders)
            ->setBuildersStack($this->buildersStack)
        ;
        $this->builders[$name] = $builder;

        return $this;
    }

    /**
     * @param array $stack
     * @return $this
     */
    public function setStack(array &$stack)
    {
        $this->stack = &$stack;

        return $this;
    }

    /**
     * @param array $builders
     * @return $this
     */
    public function setBuilders(array &$builders)
    {
        $this->builders = &$builders;

        return $this;
    }

    /**
     * @param array $builders
     * @return $this
     */
    public function setBuildersStack(array &$builders)
    {
        $this->buildersStack = &$builders;

        return $this;
    }

    public function build($name, array $args = array())
    {
        $builder = $this->builders[$name];
        $pushOnStack = false;;

        array_unshift($args, null);
        $args[0] = &$pushOnStack;

        $expr = call_user_func_array(array($builder, 'createExpression'), $args);

        $isStackEmpty = $this->isStackEmpty();

        if (!$isStackEmpty)
            $this->addChild($expr);

        if ($pushOnStack || $isStackEmpty) {
            $this->buildersStack[] = $builder;
            $this->stack[] = $expr;

            return $builder;
        }

        return $this;
    }

    public function __call($name, $args)
    {
        return $this->build($name, $args);
    }

    /**
     * @return AbstractBuilder
     */
    public function end()
    {
        array_pop($this->stack);
        array_pop($this->buildersStack);

        return $this->buildersStack[count($this->buildersStack) - 1];
    }

    abstract function createExpression(&$pushOnStack);

    abstract function addChild(Expression $expr);

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
    public function getExpression()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('Builder stack is empty');

        return $this->stack[count($this->stack) - 1];
    }
} 