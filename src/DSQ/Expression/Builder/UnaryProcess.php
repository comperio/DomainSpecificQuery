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


use Building\AbstractProcess;
use Building\Context;
use DSQ\Expression\UnaryExpression;

class UnaryProcess extends ExpressionProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $operator = 'not', $child = null, $type = null)
    {
        $unary = new UnaryExpression($operator, $child, $type);
        $newContext = new Context($context, $unary, $this);

        if (!isset($child))
            return $newContext;

        $this->finalize($newContext);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        /** @var UnaryExpression $currExpr */
        $context->object->setChild($expression);
    }
} 