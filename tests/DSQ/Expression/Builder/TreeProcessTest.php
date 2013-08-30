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
use DSQ\Expression\TreeExpression;
use DSQ\Expression\Builder\TreeProcess;

class TreeProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testBuild()
    {
        $proc = new TreeProcess;
        $context = new Context(null, '', new DummyProcess());

        $newContext = $proc->build($context, 'op');
        $expectedContext = new Context($context, new TreeExpression('op'), $proc);

        $this->assertEquals($expectedContext, $newContext);
    }

    public function testBuildWithoutGainingScope()
    {
        $context = new Context(null, '', new DummyProcess());
        $proc = $this->getMock('DSQ\Expression\Builder\TreeProcess', array('finalize'));
        $tree = new TreeExpression('op');
        $tree->setChildren(array('a', 'b', 'c'));
        $tempContext = new Context($context, $tree, $proc);

        $proc->expects($this->once())->method('finalize')->with($this->equalTo($tempContext));

        $this->assertNull($proc->build($context, 'op', 'a', 'b', 'c'));
    }

    public function testSubvalueBuilded()
    {
        $proc = new TreeProcess;
        $context = new Context(null, new TreeExpression('op', null, null), $proc);
        $tree = new TreeExpression('op');

        $proc->subvalueBuilded($context, 'a');
        $tree->addChild('a');
        $this->assertEquals($tree, $context->object);
        $proc->subvalueBuilded($context, 'b');
        $tree->addChild('b');
        $this->assertEquals($tree, $context->object);
    }
}
