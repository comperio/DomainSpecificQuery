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
    protected $builders = array();

    /** @var array Context[] */
    protected $stack = array();

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
     * @param string $name
     * @param array $args
     * @return AbstractBuilder
     */
    public function build($name, array $args = array())
    {
        $builder = $this->builders[$name];

        return call_user_func_array(array($builder, 'start'), $args);
    }

    /**
     * @param $name
     * @param $args
     * @return AbstractBuilder
     */
    public function __call($name, $args)
    {
        return $this->build($name, $args);
    }

    /**
     * @return AbstractBuilder
     */
    public function end()
    {
        $this->manipulate();
        array_pop($this->stack);

        return $this->context()->builder;
    }

    protected function addArgument($arg)
    {
        if (!$this->isStackEmpty())
            $this->context()->arguments[] = $arg;
    }

    /**
     * Returns the context to push onto the stack.
     * Null return value means that no context has to be pushed.
     *
     */
    abstract function start();

    /**
     * Do object manipulation using context args.
     *
     * @return mixed
     */
    abstract function manipulate();

    /**
     * @return bool
     */
    protected function isStackEmpty()
    {
        return !(bool) $this->stack;
    }

    /**
     * @return mixed
     *
     * @throws EmptyStackException
     */
    public function get()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('Builder stack is empty');

        $this->manipulate();

        return $this->context()->object;
    }

    /**
     * @return Context
     * @throws EmptyStackException
     */
    public function context()
    {
        if ($this->isStackEmpty())
            throw new EmptyStackException('Builder stack is empty');

        return $this->stack[count($this->stack) - 1];
    }
} 