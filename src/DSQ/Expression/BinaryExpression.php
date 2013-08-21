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


class BinaryExpression extends BasicExpression
{
    /** @var Expression */
    private $left;

    /** @var Expression */
    private $right;

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
            ->setLeft($left)
            ->setRight($right)
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
        $this->left = $this->buildExpression($left);

        return $this;
    }

    /**
     * Get Left
     *
     * @return Expression
     */
    public function getLeft()
    {
        return $this->left;
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
        $this->right = $this->buildExpression($right);

        return $this;
    }

    /**
     * Get Right
     *
     * @return Expression
     */
    public function getRight()
    {
        return $this->right;
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