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


use Building\Context;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Builder\ValueProcess;

class ValueProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $proc = $this->getMock('DSQ\Expression\Builder\ValueProcess', array('finalize'));
        $context = new Context(null, new BasicExpression('val'), $proc);
        $proc->expects($this->once())->method('finalize')->with($this->equalTo($context));

        $proc->build($context, 'val');
    }
}
 