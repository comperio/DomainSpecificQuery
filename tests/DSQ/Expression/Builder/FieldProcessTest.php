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
use DSQ\Expression\Builder\FieldProcess;
use DSQ\Expression\FieldExpression;

class FieldProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $proc = new FieldProcess();
        $context = new Context(null, '', new DummyProcess());

        $newContext = $proc->build($context, 'field');
        $expectedContext = new Context($context, new FieldExpression('field', null, '='), $proc);

        $this->assertEquals($expectedContext, $newContext);
    }

    public function testBuildWithoutGainingScope()
    {
        $context = new Context(null, '', new DummyProcess());
        $proc = $this->getMock('DSQ\Expression\Builder\FieldProcess', array('finalize'));
        $tempContext = new Context($context, new FieldExpression('field', 'value', '='), $proc);

        $proc->expects($this->once())->method('finalize')->with($this->equalTo($tempContext));

        $this->assertNull($proc->build($context, 'field', 'value'));
    }

    public function testSubvalueBuilded()
    {
        $proc = new FieldProcess;
        $context = new Context(null, new FieldExpression('field', null, '='), $proc);

        $proc->subvalueBuilded($context, 'value');
        $this->assertEquals(new FieldExpression('field', 'value', '='), $context->object);
    }
}
 