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


use DSQ\Expression\FieldExpression;

class LabelMapBuilder
{
    /**
     * @param array $map
     * @return callable
     */
    public function ary(array $map)
    {
        return function(FieldExpression $expr, LabelCompiler $compiler) use ($map)
        {
            $value = $expr->getValue();
            return new HumanReadableExpr(
                $compiler->getFieldLabel($expr->getField()),
                isset($map[$value])
                    ? $map[$value]
                    : $value
            );
        };
    }

    /**
     * @param $callback
     * @return callable
     */
    public function valueCallback($callback)
    {
        return function(FieldExpression $expr, LabelCompiler $compiler) use ($callback)
        {
            return new HumanReadableExpr(
                $compiler->getFieldLabel($expr->getField()),
                call_user_func($callback, $expr->getValue())
            );
        };
    }
} 