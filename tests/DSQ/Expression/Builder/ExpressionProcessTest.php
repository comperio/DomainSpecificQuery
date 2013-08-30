<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Expression\Builder;


class ExpressionProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testFinalize()
    {
        $context = $this->getMock('Building\Context');
        $context->expects($this->once())->method('notifyParent');

        $proc = $this->getMock('DSQ\Expression\Builder\ExpressionProcess', null);

        $proc->finalize($context);
    }
}
 