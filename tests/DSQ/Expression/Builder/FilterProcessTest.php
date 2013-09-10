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
use DSQ\Expression\BasicExpression;
use DSQ\Expression\Builder\FilterProcess;
use DSQ\Expression\FieldExpression;
use DSQ\Expression\TreeExpression;

class FilterProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $proc = new FilterProcess();
        $context = new Context(null, '', new DummyProcess());

        $newContext = $proc->build($context);
        $expectedContext = new Context($context, null, $proc);

        $this->assertEquals($expectedContext, $newContext);
    }

    public function testBuildWithoutGainingScope()
    {
        $proc = $this->getMock('DSQ\Expression\Builder\FilterProcess', array('subvalueBuilded'));
        $context = new Context(null, new TreeExpression('and'), $proc);

        $proc->expects($this->once())->method('subvalueBuilded');

        $this->assertNull($proc->build($context, 'field', 'value'));
    }

    public function testSubvalueBuilded()
    {
        $proc = new FilterProcess('or');
        $context = new Context(null, $tree = new TreeExpression('and'), $proc);

        $proc->subvalueBuilded($context, $basic = new BasicExpression('foo'));
        $children = $context->object->getChildren();
        $subtree = $children[0];
        $this->assertEquals('or', $subtree->getValue());
        $this->assertEquals(array($basic), $subtree->getChildren());
    }

    public function testSubvalueIsAddedToTheFirstMatchingSubtree()
    {
        $proc = new FilterProcess('or');
        $context = new Context(null, $tree = new TreeExpression('and'), $proc);
        $tree->addChild($or = new TreeExpression('or'));

        $proc->subvalueBuilded($context, $basic = new BasicExpression('foo'));

        $this->assertEquals(array($basic), $or->getChildren());
    }
}
