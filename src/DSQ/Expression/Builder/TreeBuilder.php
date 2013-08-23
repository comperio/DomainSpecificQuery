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


use DSQ\Expression\Expression;
use DSQ\Expression\TreeExpression;

class TreeBuilder extends AbstractBuilder
{
    function createExpression(&$pushOnStack, $value = '')
    {
        $children = func_get_args();
        array_shift($children);
        array_shift($children);

        $tree = new TreeExpression($value);

        foreach ($children as $child) {
            $tree->addChild($child);
        }

        $pushOnStack = !(bool) $children;

        return $tree;
    }

    function addChild(Expression $expr)
    {
        $this->getExpression()->addChild($expr);
        return $this;
    }

} 