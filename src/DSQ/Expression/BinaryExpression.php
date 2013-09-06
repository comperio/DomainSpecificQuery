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


class BinaryExpression extends TreeExpression
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

        $this
            ->setType($type)
            ->setValue($value)
            ->setChild($left, 0)
            ->setChild($right, 1)
        ;
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
        return $this->setChild($right, 1);
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

    /**
     * @return BinaryExpression
     */
    public function __clone()
    {
        parent::__clone();

        $this
            ->setLeft(clone($this->getLeft()))
            ->setRight(clone($this->getRight()))
        ;
    }
} 