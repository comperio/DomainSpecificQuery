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
use DSQ\Expression\BinaryExpression;
use DSQ\Expression\Builder\BinaryProcess;

class BinaryProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        $proc = new BinaryProcess;
        $context = new Context(null, '', new DummyProcess());

        $newContext = $proc->build($context, 'op');
        $expectedContext = new Context($context, new BinaryExpression('op', null, null), $proc);

        $this->assertEquals($expectedContext, $newContext);
    }

    public function testBuildWithoutGainingScope()
    {
        $context = new Context(null, '', new DummyProcess());
        $proc = $this->getMock('DSQ\Expression\Builder\BinaryProcess', array('finalize'));
        $tempContext = new Context($context, new BinaryExpression('op', 'left', 'right'), $proc);

        $proc->expects($this->once())->method('finalize')->with($this->equalTo($tempContext));

        $this->assertNull($proc->build($context, 'op', 'left', 'right'));
    }

    public function testSubvalueBuilded()
    {
        $proc = new BinaryProcess;
        $context = new Context(null, new BinaryExpression('op', null, null), $proc);

        $proc->subvalueBuilded($context, 'a');
        $this->assertEquals(new BinaryExpression('op', 'a', null), $context->object);
        $proc->subvalueBuilded($context, 'b');
        $this->assertEquals(new BinaryExpression('op', 'a', 'b'), $context->object);
    }
}
 