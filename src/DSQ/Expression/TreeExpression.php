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


class TreeExpression extends BasicExpression
{
    /**
     * @var Expression[]
     */
    private $children = array();

    /**
     * @param string $value
     * @param null $type
     */
    public function __construct($value, $type = null)
    {
        if (!isset($type))
            $type = $value;

        parent::__construct($value, $type);
    }


    /**
     * @param Expression|mixed $child
     *
     * @return $this The current instance
     */
    public function addChild($child)
     {
         $this->children[] = $this->buildExpression($child);

         return $this;
     }

    /**
     * Remove a child from the expression.
     * If $child is an integer, remove the child at the $child index.
     * If is an Expression, remove the child equals to that expression
     *
     * @param Expression|int $child
     *
     * @return $this The current instance
     */
    public function removeChild($child)
    {
        foreach ($this->children as $key => $myChild) {
            if ($child === $myChild || $key === $child)
                unset($this->children[$key]);
        }

        $this->children = array_values($this->children);

        return $this;
    }

    /**
     * @return $this The current instance
     */
    public function removeAllChildren()
    {
        $this->children = array();

        return $this;
    }

    /**
     * @return Expression[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param int $index The index of the child
     *
     * @throws \OutOfRangeException
     * @return Expression
     */
    public function getChild($index = 0)
    {
        if (isset($this->children[$index]))
            return $this->children[$index];

        throw new \OutOfRangeException("There is no child at index $index");
    }

    /**
     * Set a child at a given index
     * @param mixed|Expression $expr The index of the child
     * @param int $index The index of the child
     *
     * @return $this
     */
    public function setChild($expr, $index = 0)
    {
        $this->children[$index] = $this->buildExpression($expr);

        return $this;
    }

    /**
     * @param Expression|mixed[] $children
     *
     * @return $this The current instance
     */
    public function setChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * Has the node children?
     *
     * @return bool
     */
    public function isLeaf()
    {
        return count($this->children) == 0;
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        parent::__clone();
        $children = $this->getChildren();
        $this->removeAllChildren();

        foreach ($children as $child) {
            $this->addChild(clone($child));
        }
    }
} 