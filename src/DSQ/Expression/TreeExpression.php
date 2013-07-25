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
     * @param Expression $child
     *
     * @return $this The current instance
     */
    public function addChild(Expression $child)
     {
         $this->children[] = $child;

         return $this;
     }

    /**
     * @param Expression $child
     *
     * @return $this The current instance
     */
    public function removeChild(Expression $child)
    {
        foreach ($this->children as $key => $myChild) {
            if ($child == $myChild)
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
     * @param Expression[] $children
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
} 