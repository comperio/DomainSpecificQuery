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
        $this->expression = new BasicExpression('foo', 'fantastic-type');
    }

    public function testSetAndGetValue()
    {
        $this->assertEquals('foo', $this->expression->getValue());

        $this->expression->setValue('bar');

        $this->assertEquals('bar', $this->expression->getValue());
    }

    public function testSetAndGetType()
    {
        $this->assertEquals('fantastic-type', $this->expression->getType());

        $this->expression->setType('bad-type');

        $this->assertEquals('bad-type', $this->expression->getType());
    }

    public function testClone()
    {
        $cloned = clone($this->expression);
        $cloned->setValue('foofoo');
        $this->assertEquals('foo', $this->expression->getValue());

        $this->expression->setValue(new BasicExpression('foo'));
        $cloned = clone($this->expression);
        $cloned->getValue()->setValue('foofoo');
        $this->assertEquals('foo', $this->expression->getValue()->getValue());
    }

    public function testOffsetExists()
    {
        $expr = $this->expression;
        $expr['foo'] = 'bar';

        $this->assertFalse(isset($expr['foofoo']));
        $this->assertTrue(isset($expr['foo']));
    }

    public function testOffsetSetAndGet()
    {
        $expr = $this->expression;
        $expr['foo'] = 'bar';

        $this->assertEquals('bar', $expr['foo']);
    }

    public function testUnset()
    {
        $expr = $this->expression;
        $expr['foo'] = 'bar';
        unset($expr['foo']);

        $this->assertFalse(isset($expr['foo']));
    }
}