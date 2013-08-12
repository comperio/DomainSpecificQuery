<?php
/*
 * This file is part of DomainSpecificQuery.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DSQ\Test\Expression;

use DSQ\Expression\BasicExpression;
use DSQ\Expression\TreeExpression;

/**
 * Unit tests for class FirstClass
 */
class TreeExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TreeExpression
     */
    protected $tree;

    public function setUp()
    {
        $this->tree = new TreeExpression('foo', 'bar');
    }

    public function testAddAndGetChildren()
    {
        $this->tree
            ->addChild($child1 = new BasicExpression('child1'))
            ->addChild($child2 = new BasicExpression('child2'))
            ->addChild($child3 = new BasicExpression('child3'))
        ;

        $this->assertEquals(array($child1, $child2, $child3), $this->tree->getChildren());
    }

    public function testAddChildrenConvertNonExpressionsToBasicExpressions()
    {
        $this->tree->addChild('foo');

        $children = $this->tree->getChildren();

        $this->assertEquals('foo', $children[0]->getValue());
    }

    public function testSetChildren()
    {
        $children = array(new BasicExpression('child1'), new BasicExpression('child2'), new BasicExpression('child3'));

        $this->tree->setChildren($children);

        $this->assertEquals($children, $this->tree->getChildren());
    }

    public function testRemoveChild()
    {
        $this->tree
            ->addChild($child1 = new BasicExpression('child1'))
            ->addChild($child2 = new BasicExpression('child2'))
            ->addChild($child3 = new BasicExpression('child3'))
            ->removeChild($child2)
        ;

        $this->assertEquals(array($child1, $child3), $this->tree->getChildren());
    }

    public function testRemoveAllChildren()
    {
        $this->tree
            ->addChild($child1 = new BasicExpression('child1'))
            ->addChild($child2 = new BasicExpression('child2'))
            ->addChild($child3 = new BasicExpression('child3'))
            ->removeAllChildren()
        ;

        $this->assertEmpty($this->tree->getChildren());
    }

    public function testIsLeaf()
    {
        $this->assertTrue($this->tree->isLeaf());

        $this->tree->addChild(new BasicExpression('child'));

        $this->assertFalse($this->tree->isLeaf());
    }
}