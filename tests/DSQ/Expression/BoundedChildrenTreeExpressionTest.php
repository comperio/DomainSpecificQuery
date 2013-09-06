<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Expression;


use DSQ\Expression\BasicExpression;
use DSQ\Expression\BoundedChildrenTreeExpression;

class BoundedChildrenTreeExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddChild()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 3);

        $tree->addChild($expr);
        $this->setExpectedException('\OverflowException');
        $tree->addChild($expr);

        $this->setExpectedException('\UnderflowException');
        new BoundedChildrenTreeExpression('foo', array($expr), 2, 3);
    }


    /**
     * @expectedException \UnderflowException
     */
    public function testRemoveChild()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 3);
        $tree->removeChild(1);
    }

    /**
     * @expectedException \UnderflowException
     */
    public function testRemoveAllChildren()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 1, INF);
        $tree->removeAllChildren();
    }

    public function testSetChildWithAnExistingChild()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 2);

        $tree->setChild($expr, 0)->setChild($expr, 1);
    }

    /**
     * @expectedException \OverflowException
     */
    public function testSetChildInANewIndex()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 2);

        $tree->setChild($expr, 10);
    }

    /**
     * @expectedException \OverflowException
     */
    public function testSetChildrenWithTooMuchChildren()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 2);

        $tree->setChildren(array($expr, $expr, $expr));
    }

    /**
     * @expectedException \UnderflowException
     */
    public function testSetChildrenWithTooFewChildren()
    {
        $expr = new BasicExpression('a');
        $tree = new BoundedChildrenTreeExpression('foo', array($expr, $expr), 2, 2);

        $tree->setChildren(array($expr));
    }
}
 