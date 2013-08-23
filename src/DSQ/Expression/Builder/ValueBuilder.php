<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Expression\Builder;


use DSQ\Expression\BasicExpression;
use DSQ\Expression\Expression;

class ValueBuilder extends AbstractBuilder
{
    public function createExpression(&$pushOnStack, $value = '', $type = null)
    {
        $pushOnStack = false;
        return new BasicExpression($value, $type);
    }

    public function addChild(Expression $expr)
    {
        throw new ExpressionTypeException('Cannot add child to Value Expressions');
    }
}