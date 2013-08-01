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
use DSQ\Lucene\BasicLuceneExpression;

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

    public function testEscapeDoNothingOnLuceneExpressions()
    {
        $expr = new BasicLuceneExpression('foo');

        $this->assertEquals($expr, BasicLuceneExpression::escape($expr));
    }

    public function testEscapePhraseDoNothingOnLuceneExpressions()
    {
        $expr = new BasicLuceneExpression('foo');

        $this->assertEquals($expr, BasicLuceneExpression::escape_phrase($expr));
    }
}