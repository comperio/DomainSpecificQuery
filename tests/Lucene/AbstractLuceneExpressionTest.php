<?php
/**
 * This file is part of DomainSpecificQuery
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

namespace DSQ\Test\Lucene;


use DSQ\Lucene\AbstractLuceneExpression;
use DSQ\Lucene\TermExpression;

class AbstractLuceneExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractLuceneExpression
     */
    protected $expr;

    public function setUp()
    {
        $this->expr = $this->getMockBuilder('DSQ\Lucene\AbstractLuceneExpression')
            ->setConstructorArgs(array('value', 1.0, 'type'))
            ->setMethods(array('__toString', 'hasPrecedence'))
            ->getMock();

        $this->expr
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue(''))
        ;
    }

    public function testSetDeepValue()
    {
        $expr = $this->expr;
        $expr->setValue($last = clone($this->expr));

        $expr->setDeepValue('foo');

        $this->assertEquals('foo', $last->getValue());
        $this->assertEquals($last, $expr->getValue());
    }

    public function testSetAndGetBoost()
    {
        $expression = $this->expr;
        $expression->setBoost(12.4);

        $this->assertSame(12.4, $expression->getBoost());

        $expression->setBoost(54);
        $this->assertSame(54.0, $expression->getBoost());
    }


    public function testEscapeDoNothingOnLuceneExpressions()
    {
        $expr = new TermExpression('foo');

        $this->assertEquals($expr, TermExpression::escape($expr));
    }

    public function testEscapePhraseDoNothingOnLuceneExpressions()
    {
        $this->assertEquals($this->expr, AbstractLuceneExpression::escape_phrase($this->expr));
    }
}
 