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


use DSQ\Lucene\TermExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\TreeExpression;

class TreeExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TreeExpression
     */
    protected $expr;

    public function setUp()
    {
        $this->expr = $this->getMockBuilder('DSQ\Lucene\TreeExpression')
            ->setConstructorArgs(array('value'))
            ->setMethods(array('__toString'))
            ->getMock();

        $this->expr
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue(''))
        ;
    }

    public function testGetAndSetExpressions()
    {
        $expr = $this->expr;

        $expr->setExpressions(array(
            $e1 = new TermExpression('foo'),
            $e2 = new TermExpression('bar'),
            $e3 = new TermExpression('baz'),
        ));

        $this->assertEquals(array($e1, $e2, $e3), $expr->getExpressions());
    }

    public function testAddExpression()
    {
        $expr = $this->expr;

        $expr
            ->addExpression($e1 = new TermExpression('foo'))
            ->addExpression($e2 = new TermExpression('bar'))
            ->addExpression($e3 = new TermExpression('baz'))
        ;

        $this->assertEquals(array($e1, $e2, $e3), $expr->getExpressions());
    }

    public function testNumOfExpressions()
    {
        $expr = $this->expr;

        $expr->setExpressions(array(
            $e1 = new TermExpression('foo'),
            $e2 = new TermExpression('bar'),
            $e3 = new TermExpression('baz'),
        ));

        $this->assertEquals(3, $expr->numOfExpressions());
    }

    public function testHasPrecedence()
    {
        $expr = new BooleanExpression('+', array('foo'));
        $this->assertTrue($expr->hasPrecedence(null));

        $expr->setExpressions(array('foo', 'bar'));
        $this->assertFalse($expr->hasPrecedence(null));

        $expr->setBoost(2);
        $this->assertTrue($expr->hasPrecedence(null));
    }
}
 