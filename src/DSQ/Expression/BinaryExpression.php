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


class BinaryExpression extends BoundedChildrenTreeExpression
{
    /**
     * @param string $value
     * @param string|Expression $left
     * @param string|Expression $right
     * @param null $type
     */
    public function __construct($value, $left, $right, $type = null)
    {
        if (!isset($type))
            $type = $value;

        parent::__construct($value, array($left, $right), 2, 2, $type);
    }

    /**
     * Set Left
     *
     * @param string|Expression
     *
     * @return $this The current instance
     */
    public function setLeft($left)
    {
        return $this->setChild($left, 0);
    }

    /**
     * Get Left
     *
     * @return Expression
     */
    public function getLeft()
    {
        return $this->getChild(0);
    }

    /**
     * Set Right
     *
     * @param string|Expression $right
     *
     * @return $this The current instance
     */
    public function setRight($right)
    {
        $this->setChild($right, 1);
        return $this;
    }

    /**
     * Get Right
     *
     * @return Expression
     */
    public function getRight()
    {
        return $this->getChild(1);
    }
} 