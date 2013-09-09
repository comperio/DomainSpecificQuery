<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Compiler;

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
     * @param $label
     * @param $value
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
} 