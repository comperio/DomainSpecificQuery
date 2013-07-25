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


class ValueExpression extends BasicExpression
{
    private $value;

    /**
     * @param string $name
     * @param $value
     */
    public function __construct($name, $value)
    {
        $this
            ->setName($name)
            ->setValue($value)
        ;
    }

    /**
     * Set Value
     *
     * @param mixed $value
     *
     * @return $this The current instance
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}