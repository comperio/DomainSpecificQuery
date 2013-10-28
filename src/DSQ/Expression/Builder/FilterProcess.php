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
use DSQ\Expression\FieldExpression;

/**
 * Class FilterProcess
 * @package DSQ\Expression\Builder
 */
class FilterProcess extends ExpressionProcess
{
    private $operator;

    /**
     * @param string $operator
     */
    public function __construct($operator = 'and')
    {
        $this->operator = $operator;
    }

    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $field = null, $value = null, $type = null)
    {
        $newContext = new Context($context, null, $this);
        if (!isset($field) || !isset($value))
            return $newContext;

        $this->subvalueBuilded($newContext, new FieldExpression($field, $value, '='));

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        $this->getSubtree($context->previous->object)->addChild($expression);
    }

    /**
     * Retrieve or create the first subtree that matches with the operator
     * @param TreeExpression $expr
     * @return \DSQ\Expression\Expression|TreeExpression
     */
    private function getSubtree(TreeExpression $expr)
    {
        foreach ($expr->getChildren() as $child) {
            if ($child->getType() == $this->operator)
                return $child;
        }

        $expr->addChild($subtree = new TreeExpression($this->operator));
        return $subtree;
    }
} 