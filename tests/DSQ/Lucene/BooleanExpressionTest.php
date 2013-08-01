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


use DSQ\Lucene\BasicLuceneExpression;
use DSQ\Lucene\BooleanExpression;
use DSQ\Lucene\PhraseExpression;

class BooleanExpressionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAndSetExpressions()
    {
        $expr = new BooleanExpression;

        $expr->setExpressions(array(
            $e1 = new BasicLuceneExpression('foo'),
            $e2 = new BasicLuceneExpression('bar'),
            $e3 = new BasicLuceneExpression('baz'),
        ));

        $this->assertEquals(array($e1, $e2, $e3), $expr->getExpressions());
    }

    public function testAddExpression()
    {
        $expr = new BooleanExpression;

        $expr
            ->addExpression($e1 = new BasicLuceneExpression('foo'))
            ->addExpression($e2 = new BasicLuceneExpression('bar'))
            ->addExpression($e3 = new BasicLuceneExpression('baz'))
        ;

        $this->assertEquals(array($e1, $e2, $e3), $expr->getExpressions());
    }

    public function testNumOfExpressions()
    {
        $expr = new BooleanExpression;

        $expr->setExpressions(array(
            $e1 = new BasicLuceneExpression('foo'),
            $e2 = new BasicLuceneExpression('bar'),
            $e3 = new BasicLuceneExpression('baz'),
        ));

        $this->assertEquals(3, $expr->numOfExpressions());
    }

    public function testToStringWithSimpleValues()
    {
        $expr = new BooleanExpression('+', array('foo', 'bar', 'baz'));
        $this->assertEquals('+foo +bar +baz', $expr);

        $expr->setValue('-', $expr);
        $this->assertEquals('-foo -bar -baz', $expr);
    }

    public function testToStringWithNestedValues()
    {
        $expr = new BooleanExpression('+');

        $expr
            ->addExpression('foo')
            ->addExpression(new BooleanExpression(BooleanExpression::MUST_NOT, array('leghisti', new PhraseExpression('stupidi'))))
            ->addExpression(new BooleanExpression(BooleanExpression::SHOULD, array('povera', 'italia')))
        ;

        $this->assertEquals('+foo +(-leghisti -"stupidi") +(povera italia)', $expr);
    }

    public function testParenthesisWhenThereIsABoost()
    {
        $expr = new BooleanExpression('+', array('foo', 'bar', 'baz'), 2);

        $this->assertEquals('(+foo +bar +baz)^2', (string) $expr);
    }

    public function testParenthesisWithNestedBooleanExpressionsWithBoosts()
    {
        $expr = new BooleanExpression('+', array('foo', new BooleanExpression('', array('bar', 'bah'), 3.1), 'baz'));

        $this->assertEquals('+foo +(bar bah)^3.1 +baz', (string) $expr);
    }
}
 