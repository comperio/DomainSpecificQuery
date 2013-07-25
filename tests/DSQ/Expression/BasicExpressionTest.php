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

/**
 * Unit tests for class FirstClass
 */
class BasicExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BasicExpression
     */
    protected $expression;

    public function setUp()
    {
        $this->expression = new BasicExpression('foo');
    }

    public function testSetAndGetName()
    {
        $this->assertEquals('foo', $this->expression->getName());

        $this->expression->setName('bar');

        $this->assertEquals('bar', $this->expression->getName());
    }
}