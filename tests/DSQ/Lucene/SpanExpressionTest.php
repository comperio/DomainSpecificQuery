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


use DSQ\Lucene\SpanExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\PhraseExpression;
use DSQ\Lucene\LuceneQuery;

class SpanExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testToStringWithSimpleValues()
    {
        $expr = new SpanExpression(SpanExpression::OP_AND, array('foo', 'bar', 'baz'));
        $this->assertEquals('foo AND bar AND baz', (string) $expr);

        $expr->setValue('OR', $expr);
        $this->assertEquals('foo OR bar OR baz', (string) $expr);
    }

    public function testToStringWithNestedValues()
    {
        $expr = new SpanExpression('AND');

        $expr
            ->addExpression('foo')
            ->addExpression(new SpanExpression('SONO', array('leghisti', new PhraseExpression('stupidi'))))
            ->addExpression(new SpanExpression('OR', array('povera', 'italia')))
        ;

        $this->assertEquals('foo AND (leghisti SONO "stupidi") AND (povera OR italia)', (string) $expr);
    }

    public function testParenthesisWhenThereIsABoost()
    {
        $expr = new SpanExpression('AND', array('foo', 'bar', 'baz'), 2);

        $this->assertEquals('(foo AND bar AND baz)^2', (string) $expr);
    }

    public function testParenthesisWithNestedExpressionsWithBoosts()
    {
        $expr = new SpanExpression('AND', array('foo', new SpanExpression('OR', array('bar', 'bah'), 3.1), 'baz'));

        $this->assertEquals('foo AND (bar OR bah)^3.1 AND baz', (string) $expr);
    }

    public function testToStringWhenThereAreNoChildren()
    {
        $expr = new SpanExpression('AND');
        $this->assertEquals(LuceneQuery::ALLQUERY, (string) $expr);

        $expr = new SpanExpression('OR');
        $this->assertEquals(LuceneQuery::EMPTYQUERY, (string) $expr);
    }
}
 