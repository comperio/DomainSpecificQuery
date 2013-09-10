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


class TreeExpression extends BasicExpression implements CompositeExpression
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
     * {@inheritdoc}
     */
    public function addChild($child)
     {
         $this->children[] = $this->buildExpression($child);

         return $this;
     }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function removeAllChildren()
    {
        $this->children = array();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($index = 0)
    {
        if (isset($this->children[$index]))
            return $this->children[$index];

        throw new \OutOfRangeException("There is no child at index $index");
    }

    /**
     * {@inheritdoc}
     */
    public function setChild($expr, $index = 0)
    {
        $this->children[$index] = $this->buildExpression($expr);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren(array $children)
    {
        $this->children = array();
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
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

    /**
     * Returns the number of children
     * @return int
     */
    public function count()
    {
        return count($this->getChildren());
    }
} 