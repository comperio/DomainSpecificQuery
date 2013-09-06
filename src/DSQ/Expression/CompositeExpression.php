<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression;


interface CompositeExpression extends Expression, \Countable
{
    /**
     * Add a child to the expression
     *
     * @param Expression|mixed $child   Non-expressions child will be converted into Expressions
     *
     * @return $this The current instance
     */
    public function addChild($child);

    /**
     * Remove a child from the expression.
     * If $child is an integer, remove the child at the $child index.
     * If is an Expression, remove the child equals to that expression
     *
     * @param Expression|int $child     The child or the child index to remove
     *
     * @return $this The current instance
     */
    public function removeChild($child);

    /**
     * Remove all children
     *
     * @return $this The current instance
     */
    public function removeAllChildren();

    /**
     * Get the array of children
     * @return Expression[]
     */
    public function getChildren();

    /**
     * Get a child at a given index
     * @param int $index The index of the child
     *
     * @throws \OutOfRangeException
     * @return Expression
     */
    public function getChild($index = 0);

    /**
     * Set a child at a given index
     * @param mixed|Expression $expr The index of the child
     * @param int $index The index of the child
     *
     * @return $this
     */
    public function setChild($expr, $index = 0);

    /**
     * @param Expression|mixed[] $children
     *
     * @return $this The current instance
     */
    public function setChildren(array $children);

    /**
     * Has the node children?
     *
     * @return bool
     */
    public function isLeaf();
} 