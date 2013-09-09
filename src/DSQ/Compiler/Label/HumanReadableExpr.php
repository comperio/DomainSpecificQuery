<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler\Label;

/**
 * Class HumanReadableExpr
 *
 * This class represents a human readable version of an expression
 *
 * @package DSQ\Compiler
 */
class HumanReadableExpr
{
    private $label;
    private $value;

    /**
     * @param string $label
     * @param string|HumanReadableExpr[] $value
     */
    public function __construct($label, $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string|HumanReadableExpr[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        if (!is_array($this->getValue()))
            return array($this->getLabel(), $this->getValue());

        $ary = array($this->getLabel(), array());

        foreach ($this->getValue() as $hrExpr)
            $ary[1][] = $hrExpr->toArray();

        return $ary;
    }
} 