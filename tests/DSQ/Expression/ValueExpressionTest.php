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

use DSQ\Expression\ValueExpression;

/**
 * Unit tests for class FirstClass
 */
class ValueExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValueExpression
     */
    protected $expression;

    public function setUp()
    {
        $this->expression = new ValueExpression('foo', 'bar');
    }

    public function testSetAndGetName()
    {
        $this->assertEquals('foo', $this->expression->getName());
        $this->assertEquals('bar', $this->expression->getValue());

        $this->expression->setValue(array(1, 2, 3));

        $this->assertEquals(array(1, 2, 3), $this->expression->getValue());
    }
}