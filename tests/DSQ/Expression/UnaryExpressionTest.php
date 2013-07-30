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

use DSQ\Expression\Expression;
use DSQ\Expression\BasicExpression;
use DSQ\Expression\UnaryExpression;

/**
 * Unit tests for class UnaryExpression
 */
class UnaryExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnaryExpression
     */
    protected $expression;

    /**
     * @var Expression
     */
    protected $child;

    /**
     * @var Expression
     */
    protected $right;

    public function setUp()
    {
        $this->child= new BasicExpression(1983);

        $this->expression = new UnaryExpression('-', $this->child);
    }

    public function testSetAndGetChild()
    {
        $this->assertEquals($this->child, $this->expression->getChild());

        $this->expression
            ->setChild($child = new BasicExpression(123))
        ;

        $this->assertEquals($child, $this->expression->getChild());
    }

    public function testSetChildWithAPlainValue()
    {
        $this->expression->setChild('plain');
        $child = $this->expression->getChild();

        $this->assertInstanceOf('DSQ\Expression\BasicExpression', $child);
        $this->assertEquals('plain', $child->getValue());
    }
}