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
use DSQ\Expression\TreeExpression;

class TreeProcess extends AbstractProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $value = null)
    {
        $children = array_slice(func_get_args(), 2);

        $tree = new TreeExpression(isset($value) ? $value : $context->name);
        $newContext = new Context($context, $tree, $this);

        if (!$children)
            return $newContext;

        $tree->setChildren($children);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        $context->object->addChild($expression);
    }

    public function finalize(Context $context)
    {
        $context->notifyParent();
    }
} 