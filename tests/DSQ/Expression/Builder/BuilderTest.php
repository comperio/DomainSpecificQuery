<?php
/*
 * This file is part of DomainSpecificQuery.
 *
 * (c) 2013 Nicolò Martini
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DSQ\Test\Expression\Builder;

use DSQ\Expression\Builder\Builder;
use DSQ\Expression\Builder\ExpressionTypeException;
use DSQ\Expression\TreeExpression;
use DSQ\Expression\BasicExpression;

/**
 * Unit tests for class FirstClass
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    protected $builder;

    public function setUp()
    {
        $this->builder = new Builder;
    }

    public function testFieldBuilding()
    {
        $builder = $this->builder;

        $exp = $builder
                ->field('foo', 'bar')
                ->getExpression();

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $exp);

        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $exp->getLeft());
        $this->assertEquals($exp->getValue(), '=');

        $this->assertEquals('foo', $exp->getLeft()->getValue());
        $this->assertEquals('bar', $exp->getRight()->getValue());
    }

    public function testFieldBuildingWithOperator()
    {
        $builder = $this->builder;

        $exp = $builder->field('foo', 'bar', '>=')->getExpression();

        $this->assertEquals('>=', $exp->getValue());
    }

    public function testFieldBuildingSetExpressionTypeToLeftSubexpressionValue()
    {
        $builder = $this->builder;

        $exp = $builder->field('foo', 'bar')->getExpression();

        $this->assertEquals('foo', $exp->getType());
    }

    /**
     * @expectedException \DSQ\Expression\Builder\ExpressionTypeException
     */
    public function testWhenANonTreeNodeIsAtRootNoOtherExpressionsCanBeAdded()
    {
        $builder = $this->builder;

        $exp = $builder
                ->value('foo')
                ->field('doo', 'dah');
    }

    /**
     * @expectedException \DSQ\Expression\Builder\EmptyStackException
     */
    public function testEndWhenStackIsEmpty()
    {
        $builder = $this->builder;

        $builder
            ->field('foo', 'bar')->end()->end();
    }

    /**
     * @expectedException \DSQ\Expression\Builder\EmptyStackException
     */
    public function testGetExpressionWhenStackIsEmpty()
    {
        $builder = $this->builder;

        $builder->getExpression();
    }

    public function testUnaryBuilding()
    {
        $builder = $this->builder;

        $exp = $builder
            ->unary('-', 256)
            ->getExpression()
        ;

        $this->assertInstanceOf('DSQ\Expression\UnaryExpression', $exp);
        $this->assertEquals('-', $exp->getValue());
        $this->assertEquals(256, $exp->getChild()->getValue());

        $exp = $builder
            ->unary('-')
                ->value(256)
            ->getExpression()
        ;

        $this->assertInstanceOf('DSQ\Expression\UnaryExpression', $exp);
        $this->assertEquals('-', $exp->getValue());
        $this->assertEquals(256, $exp->getChild()->getValue());
    }

    public function testBinaryBuilding()
    {
        $builder = $this->builder;

        $exp = $builder
            ->binary('+', 1, 2)
            ->getExpression()
        ;

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $exp);
        $this->assertEquals(1, $exp->getLeft()->getValue());
        $this->assertEquals(2, $exp->getRight()->getValue());

        $exp = $builder
            ->binary('+')
                ->value(1)
                ->value(2)
            ->getExpression()
        ;

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $exp);
        $this->assertEquals(1, $exp->getLeft()->getValue());
        $this->assertEquals(2, $exp->getRight()->getValue());
    }

    public function testTreeBuilding()
    {
        $builder = $this->builder;

        /** @var TreeExpression $exp */
        $exp = $builder
            //Magic method calls for tree nodes
            ->AND()
                ->field('foo', 'bar')
                ->not('foo', 'bar')
                ->tree('OR')
                    ->field('hi', 'all')
                    ->field('my', 'god')
                ->end()
                ->value('plainValue')
            ->getExpression();

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $exp);
        $this->assertEquals('AND', $exp->getValue());
        $this->assertEquals('AND', $exp->getType());

        $children = $exp->getChildren();
        $child1 = $children[0];
        $child2 = $children[1];
        $child3 = $children[2];
        $child4 = $children[3];

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $child1);
        $this->assertEquals('AND', $exp->getValue());
        $this->assertEquals('foo', $child1->getLeft()->getValue());

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $child2);
        $this->assertEquals('not', $child2->getValue());
        $subchildren = $child2->getChildren();
        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $subchildren[0]);
        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $subchildren[1]);

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $child3);
        $this->assertEquals('OR', $child3->getValue());

        $subchildren = $child3->getChildren();

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $subchildren[0]);

        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $child4);
        $this->assertEquals('plainValue', $child4->getValue());

    }
 }