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

    /**
     * @param int $indentLevel
     * @return string
     */
    public function toString($indentLevel = 0)
    {
        $result = $this->getLabel() . ':  ';
        $value = $this->getValue();

        if (!is_array($value))
            return $result . $value;

        $result .= "\n";
        $indent = str_repeat(" ", ($indentLevel + 1) * 4);

        foreach ($value as $hrExpr) {
            $result .= $indent . $hrExpr->toString($indentLevel + 1) . "\n";
        }

        $result .= "\n";
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
} 