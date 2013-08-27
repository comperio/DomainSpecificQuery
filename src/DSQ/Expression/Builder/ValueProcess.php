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

use Building\BuildProcess;
use Building\Context;
use DSQ\Expression\BasicExpression;

class ValueProcess implements BuildProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $value = '', $type = null)
    {
        $context->process->subvalueBuilded($context, new BasicExpression($value, $type));

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function subvalueBuilded(Context $context, $expression)
    {
        // Do nothing
    }
}