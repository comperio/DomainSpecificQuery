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

    public function testTreeBuilding()
    {
        $builder = $this->builder;

        /** @var TreeExpression $exp */
        $exp = $builder
            ->tree('AND')
                ->field('foo', 'bar')
                ->field('hei', 'man')
                ->tree('OR')
                    ->field('hi', 'all')
                    ->field('my', 'god')
                ->end()
            ->getExpression();

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $exp);
        $this->assertEquals('AND', $exp->getValue());
        $this->assertEquals('AND', $exp->getType());

        $children = $exp->getChildren();
        $child1 = $children[0];
        $child2 = $children[1];
        $child3 = $children[2];

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $child1);
        $this->assertEquals('AND', $exp->getValue());
        $this->assertEquals('foo', $child1->getLeft()->getValue());

        $this->assertInstanceOf('DSQ\Expression\TreeExpression', $child3);
        $this->assertEquals('OR', $child3->getValue());

        $subchildren = $child3->getChildren();

        $this->assertInstanceOf('DSQ\Expression\BinaryExpression', $subchildren[0]);

    }
 }