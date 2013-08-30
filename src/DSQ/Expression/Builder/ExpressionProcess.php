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

abstract class ExpressionProcess extends AbstractProcess
{
    /**
     * {@inheritdoc}
     */
    public function finalize(Context $context)
    {
        $context->notifyParent();
    }
} 