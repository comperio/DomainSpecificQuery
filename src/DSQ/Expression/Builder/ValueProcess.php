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
use DSQ\Expression\BasicExpression;

class ValueProcess extends AbstractProcess
{
    /**
     * {@inheritdoc}
     */
    public function build(Context $context, $value = '', $type = 'basic')
    {
        $this->finalize(new Context($context, new BasicExpression($value, $type), $this));

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(Context $context)
    {
        $context->notifyParent();
    }
}