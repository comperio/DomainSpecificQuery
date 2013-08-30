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
use Building\DummyProcess;
use DSQ\Expression\UnaryExpression;
use DSQ\Expression\Builder\UnaryProcess;

class UnaryProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        $proc = new UnaryProcess;
        $context = new Context(null, '', new DummyProcess());

        $newContext = $proc->build($context, 'op');
        $expectedContext = new Context($context, new UnaryExpression('op', null), $proc);

        $this->assertEquals($expectedContext, $newContext);
    }

    public function testBuildWithoutGainingScope()
    {
        $context = new Context(null, '', new DummyProcess());
        $proc = $this->getMock('DSQ\Expression\Builder\UnaryProcess', array('finalize'));
        $tempContext = new Context($context, new UnaryExpression('op', 'child'), $proc);

        $proc->expects($this->once())->method('finalize')->with($this->equalTo($tempContext));

        $this->assertNull($proc->build($context, 'op', 'child'));
    }

    public function testSubvalueBuilded()
    {
        $proc = new UnaryProcess;
        $context = new Context(null, new UnaryExpression('op', null, null), $proc);

        $proc->subvalueBuilded($context, 'a');
        $this->assertEquals(new UnaryExpression('op', 'a'), $context->object);
    }
}
