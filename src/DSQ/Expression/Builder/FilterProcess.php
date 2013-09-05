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


use Building\Context;
use DSQ\Expression\TreeExpression;
use DSQ\Lucene\FieldExpression;

class FilterProcess extends ExpressionProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $field = null, $value = null, $type = null)
    {
        $expr = null;
        if (isset($field))
            $expr = new FieldExpression($field, $value, $type);

        $newContext = new Context($context, $expr, $this);

        if (!isset($value))
            return $newContext;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        $this->getSubtree($context->previous->object)->addChild($expression);
    }

    private function getSubtree(TreeExpression $expr)
    {
        foreach ($expr->getChildren() as $child)
        {
            if ($child->getType() == 'and')
                return $child;
        }

        $expr->addChild($subtree = new TreeExpression('and'));
        return $subtree;
    }
} 