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


class UnaryExpression extends BasicExpression
{
    /** @var Expression */
    private $child;

    /**
     * @param string $value
     * @param string|Expression $child
     * @param null $type
     */
    public function __construct($value, $child, $type = null)
    {
        if (!isset($type))
            $type = $value;

        $this
            ->setType($type)
            ->setValue($value)
            ->setChild($child)
        ;
    }

    /**
     * Set Child
     *
     * @param string|Expression
     *
     * @return $this The current instance
     */
    public function setChild($child)
    {
        if (!$child instanceof Expression)
            $child = new BasicExpression($child);

        $this->child = $child;

        return $this;
    }

    /**
     * Get Child
     *
     * @return Expression
     */
    public function getChild()
    {
        return $this->child;
    }
} 