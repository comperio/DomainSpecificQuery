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
use DSQ\Expression\FieldExpression;
use DSQ\Expression\UnaryExpression;

class FieldProcess extends ExpressionProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $field = '', $value = null, $type = null)
    {
        $expr = new FieldExpression($field, $value, '=', $type);
        $newContext = new Context($context, $expr, $this);

        if (!isset($value))
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
        $context->object->setValue($expression);
    }
} 